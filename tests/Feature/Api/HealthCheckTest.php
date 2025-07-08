<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /** @test */
    public function it_returns_healthy_status_when_all_services_are_up()
    {
        // Mock successful connections
        DB::shouldReceive('connection->getPdo')->andReturn(true);
        Redis::shouldReceive('ping')->andReturn(true);
        Storage::shouldReceive('disk->exists')->andReturn(true);

        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'services' => [
                    'database',
                    'redis',
                    'storage'
                ]
            ])
            ->assertJsonPath('status', 'healthy')
            ->assertJsonPath('services.database', 'connected')
            ->assertJsonPath('services.redis', 'connected')
            ->assertJsonPath('services.storage', 'available');
    }

    /** @test */
    public function it_returns_unhealthy_status_when_database_is_down()
    {
        // Mock database failure
        DB::shouldReceive('connection->getPdo')->andThrow(new \Exception('Connection failed'));
        Redis::shouldReceive('ping')->andReturn(true);
        Storage::shouldReceive('disk->exists')->andReturn(true);

        $response = $this->getJson('/api/health');

        $response->assertStatus(503)
            ->assertJsonPath('status', 'unhealthy')
            ->assertJsonPath('services.database', 'disconnected');
    }

    /** @test */
    public function it_includes_response_time_metric()
    {
        $response = $this->getJson('/api/health');

        $responseTime = $response->json('metrics.response_time_ms');
        
        $this->assertIsNumeric($responseTime);
        $this->assertGreaterThan(0, $responseTime);
        $this->assertLessThan(1000, $responseTime); // Should respond within 1 second
    }
}