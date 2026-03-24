<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Exception;

class QrCodeGenerationService
{
    /**
     * Generate a physical QR code image based on database records.
     */
    public function generateQrCode(string $content, \App\Models\QrCode $qrData): string
    {
        try {
            $qr = QrCode::format('svg')
                ->size($qrData->size)
                ->color(
                    ...$this->hexToRgb($qrData->color)
                )
                ->backgroundColor(
                    ...$this->hexToRgb($qrData->background_color)
                )
                ->margin(1);

            if ($qrData->logo_path && Storage::disk('public')->exists($qrData->logo_path)) {
                // Ensure correct relative path handling for logo
                $qr->merge('/storage/app/public/' . $qrData->logo_path, .3, true);
            }

            return (string) $qr->generate($content);
        } catch (Exception $e) {
            // Fallback generation if styled fails
            return (string) QrCode::format('svg')->size(300)->generate($content);
        }
    }

    /**
     * Convert Hex string to RGB Array
     * @return array<int, int>
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return [$r, $g, $b];
    }
}