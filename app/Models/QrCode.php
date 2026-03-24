<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = [
        'link_id',
        'color',
        'background_color',
        'size',
        'logo_path',
    ];
}
