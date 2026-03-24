<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Jenssegers\Agent\Agent;
use App\Models\Device;
use App\Models\Scan;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;

class TrackScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $linkId,
        public string $userAgent,
        public string $ipAddress
    ) {}

    public function handle(): void
    {
        try {
            $agent = new Agent();
            $agent->setUserAgent($this->userAgent);

            $deviceType = $agent->isDesktop() ? 'Desktop' : ($agent->isTablet() ? 'Tablet' : 'Mobile');
            if ($agent->isRobot()) {
                $deviceType = 'Robot';
            }

            // Find or create device record
            $device = Device::firstOrCreate(
                ['name' => $deviceType, 'os' => $agent->platform() ?: 'Unknown']
            );

            // Fetch country from IP
            $country = 'Unknown';
            try {
                if ($position = Location::get($this->ipAddress)) {
                    $country = $position->countryName ?? 'Unknown';
                }
            } catch (\Exception $e) {
                Log::warning("Failed to resolve location for IP: {$this->ipAddress}. Error: " . $e->getMessage());
            }

            Scan::create([
                'link_id' => $this->linkId,
                'device_id' => $device->id,
                'ip_address' => $this->ipAddress,
                'user_agent' => $this->userAgent,
                'country' => $country,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to track scan: ' . $e->getMessage());
        }
    }
}
