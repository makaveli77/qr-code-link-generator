<?php

namespace App\Queries;

use App\Models\Link;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserLinksQuery
{
    public function getForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Link::where('user_id', $user->id)
            ->with('qrCode')
            ->latest()
            ->paginate($perPage);
    }

    public function findByShortCode(string $shortCode): ?Link
    {
        return Link::where('short_code', $shortCode)->first();
    }

    public function findWithQrCode(string $shortCode): Link
    {
        return Link::with('qrCode')->where('short_code', $shortCode)->firstOrFail();
    }

    /**
     * Finds a link by short code for the redirect logic, including only required columns.
     */
    public function findForRedirect(string $shortCode): ?Link
    {
        return Link::where('short_code', $shortCode)
            ->first(['id', 'short_code', 'original_url', 'expires_at', 'password']);
    }
}
