<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Force rate limiting to be enabled in tests
        \Illuminate\Support\Facades\RateLimiter::for('api', function ($request) {
            // Use only IP for the key to match test requests
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });
        $this->withMiddleware();
    }
}
