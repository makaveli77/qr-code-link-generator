<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function show(string $shortCode): JsonResponse
    {
        $link = Link::where('short_code', $shortCode)->firstOrFail();

        $analytics = $this->analyticsService->getAnalytics($link);

        return response()->json(array_merge([
            'short_code' => $shortCode,
            'original_url' => $link->original_url,
        ], $analytics));
    }
}