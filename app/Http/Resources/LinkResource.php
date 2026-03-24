<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'original_url' => $this->original_url,
            'short_code' => $this->short_code,
            'short_url' => url('/' . $this->short_code),
            'qr_code_download_url' => route('api.links.qr_download', $this->short_code),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'qr_code_settings' => [
                'color' => $this->qrCode->color ?? '#000000',
                'background_color' => $this->qrCode->background_color ?? '#ffffff',
                'size' => $this->qrCode->size ?? 300,
                'has_logo' => !empty($this->qrCode->logo_path),
            ]
        ];
    }
}