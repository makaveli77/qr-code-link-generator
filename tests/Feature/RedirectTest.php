<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Models\Link;
use App\Jobs\TrackScanJob;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_short_link_redirects_to_original_url()
    {
        \Illuminate\Support\Facades\Cache::flush();
        Queue::fake();

        $link = Link::create([
            'original_url' => 'https://test-destination.com/landing',
            'short_code' => 'abcde123'
        ]);

        $response = $this->get('/abcde123');

        $response->assertStatus(302);
        $response->assertRedirect('https://test-destination.com/landing');

        // Assert that the job was dispatched to track the analytics
        Queue::assertPushed(TrackScanJob::class, function ($job) use ($link) {
            return $job->linkId === $link->id;
        });
    }

    public function test_redirect_returns_404_if_link_does_not_exist()
    {
        $response = $this->get('/nonexistent123');
        $response->assertStatus(404);
    }

    public function test_redirect_returns_410_if_link_is_expired()
    {
        Link::create([
            'original_url' => 'https://test.com',
            'short_code' => 'expired123',
            'expires_at' => now()->subDay()
        ]);

        $response = $this->get('/expired123');
        $response->assertStatus(410);
    }

    public function test_redirect_requires_password_for_protected_link()
    {
        $link = \App\Models\Link::create([
            'original_url' => 'https://secure.com',
            'short_code' => 'secure123',
            'password' => bcrypt('secret123'),
        ]);

        // No password provided
        $response = $this->get('/secure123');
        $response->assertStatus(401);
        $response->assertSee('Password required');

        // Wrong password
        $response = $this->get('/secure123?password=wrongpass');
        $response->assertStatus(401);
        $response->assertSee('Password required');

        // Correct password
        $response = $this->get('/secure123?password=secret123');
        $response->assertStatus(302);
        $response->assertRedirect('https://secure.com');
    }
}
