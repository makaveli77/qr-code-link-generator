<?php

namespace App\Queries;

use App\Models\Link;
use Illuminate\Support\Facades\DB;

class LinkAnalyticsQuery
{
    public function getSummary(Link $link): array
    {
        return [
            'total_scans' => $link->scans()->count(),
            'device_breakdown' => $this->getDeviceBreakdown($link),
            'country_breakdown' => $this->getCountryBreakdown($link),
            'daily_scans' => $this->getDailyScans($link),
        ];
    }

    private function getDeviceBreakdown(Link $link)
    {
        return $link->scans()
            ->join('devices', 'scans.device_id', '=', 'devices.id')
            ->select('devices.name', DB::raw('count(*) as count'))
            ->groupBy('devices.name')
            ->pluck('count', 'name');
    }

    private function getCountryBreakdown(Link $link)
    {
        return $link->scans()
            ->select('country', DB::raw('count(*) as count'))
            ->groupBy('country')
            ->pluck('count', 'country');
    }

    private function getDailyScans(Link $link)
    {
        return $link->scans()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
    }
}
