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
        return $this->linkService->getUserLinks($request->user()->id);
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
    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
            'title' => 'nullable|string|max:255',
        ]);

        $link = $this->linkService->createLink(
            $request->user()->id,
            $request->only(['original_url', 'title'])
        );

        return response()->json($link, 201);
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
    public function show($id)
    {
        $link = Link::findOrFail($id);
        $this->authorize('view', $link);
        return $link->load('qrCode');
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
    public function update(Request $request, $id)
    {
        $link = Link::findOrFail($id);
        $this->authorize('update', $link);

        $request->validate([
            'original_url' => 'sometimes|url',
            'title' => 'nullable|string|max:255',
        ]);

        $link = $this->linkService->updateLink($link, $request->only(['original_url', 'title']));
        return response()->json($link);
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
    public function destroy($id)
    {
        $link = Link::findOrFail($id);
        $this->authorize('delete', $link);
        $link->delete();
        return response()->json(null, 204);
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
    public function updateBranding(Request $request, $id)
    {
        $link = Link::findOrFail($id);
        $this->authorize('update', $link);

        $request->validate([
            'color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'eye_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'logo_url' => 'nullable|url',
            'style' => 'nullable|string|in:square,round,dot',
        ]);

        $qrCode = $this->qrCodeService->updateBranding($link->qrCode, $request->all());
        return response()->json($qrCode);
    }
}
