<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AnalyticsController extends Controller
{
    public function __construct(protected AnalyticsService $analyticsService) {}

    #[OA\Get(
        path: "/analytics/overview",
        summary: "Get an overview of all link analytics for the user",
        security: [["bearerAuth" => []]],
        tags: ["Analytics"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Analytics overview",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "total_clicks", type: "integer", example: 1540),
                        new OA\Property(property: "total_links", type: "integer", example: 5),
                        new OA\Property(property: "top_links", type: "array", items: new OA\Items(type: "object"))
                    ]
                )
            )
        ]
    )]
    public function overview(Request $request)
    {
        return $this->analyticsService->getUserOverview($request->user()->id);
    }

    #[OA\Get(
        path: "/links/{id}/analytics",
        summary: "Get detailed analytics for a specific link",
        security: [["bearerAuth" => []]],
        tags: ["Analytics"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Link analytics data"),
            new OA\Response(response: 404, description: "Link not found")
        ]
    )]
    public function linkAnalytics(Request $request, $id)
    {
        $link = Link::findOrFail($id);
        $this->authorize('view', $link);
        return $this->analyticsService->getLinkAnalytics($link->id);
    }
}
