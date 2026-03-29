<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Services\LinkService;
use App\Services\QrCodeGenerationService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LinkController extends Controller
{
    public function __construct(
        protected LinkService $linkService,
        protected QrCodeGenerationService $qrCodeService
    ) {}

    #[OA\Get(
        path: "/links",
        summary: "Get all links for the authenticated user",
        security: [["bearerAuth" => []]],
        tags: ["Links"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of links",
                content: new OA\JsonContent(type: "array", items: new OA\Items(type: "object"))
            )
        ]
    )]
    public function index(Request $request)
    {
        $links = Link::where('user_id', $request->user()->id)
            ->with('qrCode')
            ->paginate();
            
        return \App\Http\Resources\LinkResource::collection($links);
    }

    #[OA\Post(
        path: "/links",
        summary: "Create a new short link",
        security: [["bearerAuth" => []]],
        tags: ["Links"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["original_url"],
                properties: [
                    new OA\Property(property: "original_url", type: "string", format: "uri", example: "https://google.com"),
                    new OA\Property(property: "title", type: "string", example: "Google Search")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Link created"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(\App\Http\Requests\StoreLinkRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()?->id;
        
        // Map 'title' fallback since validation doesn't include it in StoreLinkRequest currently
        $data['title'] = $request->input('title');
        
        // Use logo_path if available (since the valid property in DTO is logoPath, usually handled via logo upload but kept simple here)
        $data['logo_path'] = $request->input('logo_path');

        $dto = \App\DTOs\CreateLinkDTO::fromArray($data);
        $link = $this->linkService->createLink($dto);

        return response()->json([
            'data' => (new \App\Http\Resources\LinkResource($link))->toArray($request)
        ], 201);
    }

    #[OA\Get(
        path: "/links/{id}",
        summary: "Get a specific link",
        security: [["bearerAuth" => []]],
        tags: ["Links"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Link details"),
            new OA\Response(response: 404, description: "Link not found")
        ]
    )]
    public function show(Link $link)
    {
        $this->authorize('view', $link);
        return new \App\Http\Resources\LinkResource($link->load('qrCode'));
    }

    #[OA\Put(
        path: "/links/{id}",
        summary: "Update an existing link",
        security: [["bearerAuth" => []]],
        tags: ["Links"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "original_url", type: "string", format: "uri"),
                    new OA\Property(property: "title", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Link updated"),
            new OA\Response(response: 404, description: "Link not found")
        ]
    )]
    public function update(Request $request, Link $link)
    {
        $this->authorize('update', $link);

        $request->validate([
            'original_url' => 'sometimes|url',
            'title' => 'nullable|string|max:255',
        ]);

        $link = $this->linkService->updateLink($link, $request->only(['original_url', 'title']));
        return new \App\Http\Resources\LinkResource($link);
    }

    #[OA\Delete(
        path: "/links/{id}",
        summary: "Delete a link",
        security: [["bearerAuth" => []]],
        tags: ["Links"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 204, description: "Link deleted"),
            new OA\Response(response: 404, description: "Link not found")
        ]
    )]
    public function destroy(Link $link)
    {
        $this->authorize('delete', $link);
        $link->delete();
        return response()->json(['message' => 'Link deleted successfully.'], 200);
    }

    #[OA\Post(
        path: "/links/{id}/qr-branding",
        summary: "Update QR code branding",
        security: [["bearerAuth" => []]],
        tags: ["Links"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "color", type: "string", example: "#000000"),
                    new OA\Property(property: "eye_color", type: "string", example: "#ff0000"),
                    new OA\Property(property: "logo_url", type: "string", format: "uri"),
                    new OA\Property(property: "style", type: "string", enum: ["square", "round", "dot"])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Branding updated"),
            new OA\Response(response: 404, description: "Link not found")
        ]
    )]
    public function updateBranding(Request $request, Link $link)
    {
        $this->authorize('update', $link);

        $request->validate([
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'eye_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'logo_url' => 'nullable|url',
            'style' => 'nullable|string|in:square,round,dot',
        ]);

        $qrCode = $this->qrCodeService->updateBranding($link->qrCode, $request->all());
        return response()->json($qrCode);
    }

    public function downloadQr($shortCode)
    {
        $link = Link::where('short_code', $shortCode)->firstOrFail();
        
        $qrData = $link->qrCode;

        if (!$qrData) {
            abort(404, 'QR Code settings not found.');
        }

        $svg = $this->qrCodeService->generateQrCode($link->short_url, $qrData);

        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="qrcode-'.$shortCode.'.svg"');
    }
}
