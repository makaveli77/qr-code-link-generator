<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrCode extends Model
{
    protected $fillable = [
        'link_id',
        'color',
        'background_color',
        'size',
        'logo_path',
    ];

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
