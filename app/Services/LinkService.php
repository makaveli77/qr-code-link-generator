<?php

namespace App\Services;

use App\DTOs\CreateLinkDTO;
use App\Models\Link;
use App\Repositories\LinkRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class LinkService
{
    public function __construct(
        protected LinkRepository $linkRepository
    ) {}

    public function createLink(CreateLinkDTO $dto): Link
    {
        // Generate unique short code or use custom alias
        $shortCode = $dto->customAlias ?: $this->generateUniqueShortCode();

        // Handle SoftDeleted links with the same alias
        if ($dto->customAlias) {
            $existing = Link::withTrashed()->where('short_code', $dto->customAlias)->first();
            if ($existing) {
                $existing->forceDelete();
            }
        }

        $linkData = [
            'user_id' => $dto->userId,
            'original_url' => $dto->originalUrl,
            'short_code' => $shortCode,
            'expires_at' => $dto->expiresAt,
            'password' => $dto->password ? Hash::make($dto->password) : null,
        ];

        $qrData = [
            'color' => $dto->color,
            'background_color' => $dto->backgroundColor,
            'size' => $dto->size,
            'logo_path' => $dto->logoPath,
        ];

        return $this->linkRepository->create($linkData, $qrData);
    }

    private function generateUniqueShortCode(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (Link::withTrashed()->where('short_code', $code)->exists());

        return $code;
    }

    public function updateLink(Link $link, array $data): Link
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        if (!empty($data['custom_alias'])) {
            $data['short_code'] = $data['custom_alias'];
        }

        $this->linkRepository->update($link, $data);
        return $link->fresh('qrCode');
    }

    public function updateQrBranding(Link $link, array $data, $logoFile = null): Link
    {
        if ($logoFile) {
            $data['logo_path'] = $logoFile->store('qrcodes/logos', 'public');
        }

        $this->linkRepository->updateQrCode($link, $data);
        return $link->fresh('qrCode');
    }
}