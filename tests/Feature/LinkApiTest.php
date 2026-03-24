<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Link;

class LinkApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_qr_link()
    {
        $payload = [
            'original_url' => 'https://example.com/product/123',
            'color' => '#FF0000',
            'background_color' => '#FFFFFF',
            'size' => 400
        ];

        $response = $this->postJson('/api/links', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'original_url',
                    'short_code',
                    'short_url',
                    'qr_code_download_url',
                    'qr_code_settings'
                ]
            ]);

        $this->assertDatabaseHas('links', [
            'original_url' => 'https://example.com/product/123'
        ]);

        $this->assertDatabaseHas('qr_codes', [
            'color' => '#FF0000',
            'size' => 400
        ]);
    }

    public function test_validation_fails_on_invalid_url()
    {
        $response = $this->postJson('/api/links', [
            'original_url' => 'not-a-url'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['original_url']);
    }

    public function test_validation_fails_on_invalid_hex_color()
    {
        $response = $this->postJson('/api/links', [
            'original_url' => 'https://example.com',
            'color' => 'red' // Invalid format, expects HEX
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['color']);
    }

    public function test_can_retrieve_analytics_for_link()
    {
        $link = \App\Models\Link::create([
            'original_url' => 'https://example.com',
            'short_code' => 'xyz123'
        ]);

        $response = $this->getJson("/api/analytics/xyz123");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'short_code',
                'original_url',
                'total_scans',
                'analytics' => [
                    'devices',
                    'countries',
                    'daily_scans'
                ]
            ]);
    }

    public function test_can_create_a_link_with_custom_alias()
    {
        $payload = [
            'original_url' => 'https://example.com/promo',
            'custom_alias' => 'summer-sale',
        ];

        $response = $this->postJson('/api/links', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.short_code', 'summer-sale');

        $this->assertDatabaseHas('links', [
            'original_url' => 'https://example.com/promo',
            'short_code' => 'summer-sale'
        ]);
    }

    public function test_validation_fails_on_duplicate_custom_alias()
    {
        Link::create([
            'original_url' => 'https://example.com',
            'short_code' => 'taken-alias'
        ]);

        $response = $this->postJson('/api/links', [
            'original_url' => 'https://example.com/new',
            'custom_alias' => 'taken-alias'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['custom_alias']);
    }

    public function test_can_create_a_link_with_expiration_date()
    {
        $expiresAt = now()->addDays(7)->toIso8601String();
        
        $payload = [
            'original_url' => 'https://example.com/promo',
            'expires_at' => $expiresAt,
        ];

        $response = $this->postJson('/api/links', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('links', [
            'original_url' => 'https://example.com/promo',
            'expires_at' => now()->addDays(7)->toDateTimeString()
        ]);
    }

    public function test_user_can_list_their_links()
    {
        $user = \App\Models\User::factory()->create();
        \App\Models\Link::factory()->count(3)->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');
        $response = $this->getJson('/api/links');
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_user_can_update_their_link()
    {
        $user = \App\Models\User::factory()->create();
        $link = \App\Models\Link::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');
        $response = $this->putJson("/api/links/{$link->id}", [
            'original_url' => 'https://updated.com',
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('data.original_url', 'https://updated.com');
        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'original_url' => 'https://updated.com',
        ]);
    }

    public function test_user_can_delete_their_link()
    {
        $user = \App\Models\User::factory()->create();
        $link = \App\Models\Link::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');
        $response = $this->deleteJson("/api/links/{$link->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Link deleted successfully.']);

        $this->assertSoftDeleted('links', [
            'id' => $link->id
        ]);
    }
}
