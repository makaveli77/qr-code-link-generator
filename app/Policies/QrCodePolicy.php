<?php

namespace App\Policies;

use App\Models\QrCode;
use App\Models\User;

class QrCodePolicy
{
    public function update(User $user, QrCode $qrCode): bool
    {
        return $user->id === $qrCode->link->user_id;
    }

    public function delete(User $user, QrCode $qrCode): bool
    {
        return $user->id === $qrCode->link->user_id;
    }
}
