<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = [
        'name',
        'os',
    ];

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }
}
