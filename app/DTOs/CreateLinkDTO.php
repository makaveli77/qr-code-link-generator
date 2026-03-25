<?php

namespace App\DTOs;

use Illuminate\Support\Carbon;

class CreateLinkDTO
{
    public function __construct(
        public readonly string $originalUrl,
        public readonly ?int $userId = null,
        public readonly ?string $customAlias = null,
        public readonly ?Carbon $expiresAt = null,
        public readonly string $color = '#000000',
        public readonly string $backgroundColor = '#ffffff',
        public readonly int $size = 300,
        public readonly ?string $logoPath = null,
        public readonly ?string $password = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            originalUrl: $data['original_url'],
            userId: $data['user_id'] ?? null,
            customAlias: $data['custom_alias'] ?? null,
            expiresAt: isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null,
            color: $data['color'] ?? '#000000',
            backgroundColor: $data['background_color'] ?? '#ffffff',
            size: $data['size'] ?? 300,
            logoPath: $data['logo_path'] ?? null,
            password: $data['password'] ?? null
        );
    }
}
