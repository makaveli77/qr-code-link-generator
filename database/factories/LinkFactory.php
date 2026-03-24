<?php

namespace Database\Factories;

use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LinkFactory extends Factory
{
    protected $model = Link::class;

    public function definition()
    {
        return [
            'original_url' => $this->faker->url(),
            'short_code' => Str::random(6),
            'user_id' => null, // Set in test as needed
            'expires_at' => null,
        ];
    }
}
