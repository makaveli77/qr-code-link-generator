<?php

namespace App\Services;

use App\Models\Link;
use App\Models\Scan;
use App\Queries\LinkAnalyticsQuery;

class AnalyticsService
{
    public function __construct(
        protected LinkAnalyticsQuery $query
    ) {}

    public function getAnalytics(Link $link): array
    {
        $summary = $this->query->getSummary($link);

        return [
            'total_scans' => $summary['total_scans'],
            'analytics' => [
                'devices' => $summary['device_breakdown'],
                'countries' => $summary['country_breakdown'],
                'daily_scans' => $summary['daily_scans'],
            ]
        ];
    }

    public function getUserOverview(int $userId): array
    {
        $totalLinks = Link::where('user_id', $userId)->count();

        $totalClicks = Scan::whereHas('link', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $topLinks = Link::where('user_id', $userId)
            ->withCount('scans')
            ->orderByDesc('scans_count')
            ->take(5)
            ->get();

        return [
            'total_clicks' => $totalClicks,
            'total_links' => $totalLinks,
            'top_links' => $topLinks,
        ];
    }
}
