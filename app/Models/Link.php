<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'custom_alias',
        'expires_at',
        'password',
        'color',
        'branding',
        'title',
        'description',
    ];
    // 'password' is mass assignable (guarded = [] means all fields are mass assignable)

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }
    
    public function scans()
    {
        return $this->hasMany(Scan::class);
    }

    public function getShortUrlAttribute()
    {
        return url("/" . $this->short_code);
    }
}
