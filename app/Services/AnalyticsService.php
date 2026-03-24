<?php

namespace App\Services;

use App\Models\Link;
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
}
