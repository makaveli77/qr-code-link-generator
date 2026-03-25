<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_api_tokens()
    {
        $user = User::factory()->create();
        $user->createToken('token1');
        $user->createToken('token2');
        $token = $user->createToken('main')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/tokens');

        $response->assertStatus(200)
            ->assertJsonStructure(['tokens' => [['id', 'name', 'last_used_at', 'created_at']]]);
        $this->assertCount(3, $response->json('tokens'));
    }

    public function test_create_api_token()
    {
        $user = User::factory()->create(['is_partner' => true]);
        $token = $user->createToken('main')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tokens', ['name' => 'new-token']);

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token', 'token_type']);
    }

    public function test_revoke_api_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('main')->plainTextToken;
        $otherToken = $user->createToken('to-delete');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/tokens/' . $otherToken->accessToken->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Token revoked successfully.']);
    }

}
