<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AIController extends Controller
{
    /**
     * Act as an AI copywriter to suggest dynamic QR link CTAs.
     */
    public function generateQrContent(Request $request): JsonResponse
    {
        $request->validate([
            'scenario' => 'required|string|max:500',
        ]);

        $scenario = $request->input('scenario');

        $systemPrompt = "You are an expert marketing assistant for a QR code platform. "
            . "A user will describe a scenario for their QR code. "
            . "Reply with 3 creative, short, and punchy Call-To-Action (CTA) phrases they should place alongside the QR Code. "
            . "Keep the CTAs short (under 7 words each). Provide only the list of CTAs.\n\n"
            . "Scenario: " . $scenario;

        try {
            // Using Gemini to generate the CTAs
            $response = Gemini::generativeModel('gemini-flash-latest')->generateContent($systemPrompt);

            return response()->json([
                'success' => true,
                'ctas' => array_filter(array_map('trim', explode("\n", $response->text())))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Ensure your GEMINI_API_KEY is configured.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * RAG Search for Tech Support & Strategy Consultant
     */
    public function askSupport(Request $request): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $question = $request->input('question');

        try {
            // Embed the user's question
            $embeddingResponse = Gemini::embeddingModel('gemini-embedding-001')->embedContent($question);
            $questionEmbedding = $embeddingResponse->embedding->values;
            $vectorString = '[' . implode(',', $questionEmbedding) . ']';

            // Perform Similarity Search using pgvector in PostgreSQL
            // We use the <-> operator which calculates L2 distance (cosine distance). The closest comes first.
            $closestDocs = DB::select('
                SELECT title, content, embedding <-> ?::vector AS distance
                FROM documents
                ORDER BY distance ASC
                LIMIT 2
            ', [$vectorString]);

            // Build context from the closest docs
            $contextText = "";
            foreach ($closestDocs as $doc) {
                $contextText .= "--- Document: " . $doc->title . " ---\n";
                $contextText .= $doc->content . "\n\n";
            }

            // Ask Gemini the final question packed with context
            $prompt = "You are a helpful customer support and strategy consultant for a QR Code platform.\n"
                . "Use ONLY the following context to answer the user's question. If the answer is not in the context, say 'I don't have information on that, please contact human support.'\n\n"
                . "CONTEXT:\n" . $contextText . "\n"
                . "USER QUESTION: " . $question . "\n\n"
                . "ANSWER:";

            $chatResponse = Gemini::generativeModel('gemini-flash-latest')->generateContent($prompt);

            return response()->json([
                'success' => true,
                'answer' => $chatResponse->text()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to answer question.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unified RAG & Agent Endpoint
     * Combines Knowledge Base (RAG) with the user's Live Database State.
     */
    public function askAgent(Request $request): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $question = $request->input('question');
        $user = $request->user();

        try {
            // --- Gather Knowledge Base Context (RAG) ---
            $embeddingResponse = Gemini::embeddingModel('gemini-embedding-001')->embedContent($question);
            $questionEmbedding = $embeddingResponse->embedding->values;
            $vectorString = '[' . implode(',', $questionEmbedding) . ']';

            $closestDocs = DB::select('
                SELECT title, content, embedding <-> ?::vector AS distance
                FROM documents
                ORDER BY distance ASC
                LIMIT 2
            ', [$vectorString]);

            $ragContext = "";
            foreach ($closestDocs as $doc) {
                $ragContext .= "--- Document: {$doc->title} ---\n{$doc->content}\n\n";
            }

            // --- Gather User Database State Context ---
            // Instead of doing slow round-trip function calls, we eager-load cheap analytics
            // so the LLM acts as an Agent that already "knows" the user's state securely.
            $totalLinks = $user->links()->count();
            $totalScans = \App\Models\Scan::whereHas('link', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();
            $partnerStatus = $user->is_partner ? 'Partner / Pro' : 'Free Tier';

            $userContext = "--- Logged-In User Database State ---\n";
            $userContext .= "Name: {$user->name}\n";
            $userContext .= "Email: {$user->email}\n";
            $userContext .= "Account Tier: {$partnerStatus}\n";
            $userContext .= "Total QR Links Created: {$totalLinks}\n";
            $userContext .= "Total Total QR Scans Received: {$totalScans}\n\n";

            // --- Prompt the Agent ---
            $prompt = "You are a friendly, highly intelligent Tech Support and Strategy Agent for our QR Code platform.\n"
                . "You are chatting directly with user {$user->name}.\n"
                . "If they ask about their account, limits, or analytics, rely ONLY on the 'Logged-In User Database State' provided below. "
                . "If they ask about how to use the app, limits policies, or strategies, use the Knowledge Base CONTEXT provided.\n"
                . "Keep answers concise, professional, and directly address the user.\n\n"
                . "=== KNOWLEDGE BASE CONTEXT ===\n" . $ragContext
                . "=== USER DATABASE STATE ===\n" . $userContext
                . "=== USER QUESTION ===\n" . $question . "\n\n"
                . "AGENT ANSWER:";

            // Gemini 1.5 Flash is much faster for agent tasks
            $chatResponse = Gemini::generativeModel('gemini-flash-latest')->generateContent($prompt);

            return response()->json([
                'success' => true,
                'answer' => $chatResponse->text()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reach agent.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
