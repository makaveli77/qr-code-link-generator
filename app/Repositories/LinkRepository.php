<?php

namespace App\Repositories;

use App\Models\Link;
use App\Models\QrCode;
use Illuminate\Support\Facades\DB;

class LinkRepository
{
    public function create(array $linkData, array $qrData): Link
    {
        return DB::transaction(function () use ($linkData, $qrData) {
            $link = Link::create($linkData);
            $link->qrCode()->create($qrData);
            return $link->load('qrCode');
        });
    }

    public function update(Link $link, array $data): bool
    {
        return $link->update($data);
    }

    public function delete(Link $link): bool
    {
        return $link->delete();
    }

    public function updateQrCode(Link $link, array $data): bool
    {
        $qr = $link->qrCode ?: $link->qrCode()->create();
        return $qr->update($data);
    }
}
