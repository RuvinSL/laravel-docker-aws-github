<?php

namespace Tests\Integration\Cache;

use App\Services\CacheService;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RedisCacheTest extends TestCase
{
    private CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = new CacheService();
        Cache::flush();
    }

    /** @test */
    public function it_caches_expensive_queries()
    {
        $users = User::factory()->count(100)->create();
        
        // First call - should hit database
        $startTime = microtime(true);
        $result1 = $this->cacheService->getTopUsers(10);
        $dbTime = microtime(true) - $startTime;
        
        // Second call - should hit cache
        $startTime = microtime(true);
        $result2 = $this->cacheService->getTopUsers(10);
        $cacheTime = microtime(true) - $startTime;
        
        // Assert results are the same
        $this->assertEquals($result1->pluck('id'), $result2->pluck('id'));
        
        // Assert cache is faster (at least 10x)
        $this->assertLessThan($dbTime / 10, $cacheTime);
    }

    /** @test */
    public function it_invalidates_cache_on_data_change()
    {
        $users = User::factory()->count(5)->create();
        
        // Cache the data
        $result1 = $this->cacheService->getTopUsers(10);
        $this->assertCount(5, $result1);
        
        // Create new user - should invalidate cache
        $newUser = User::factory()->create(['score' => 9999]);
        
        // Get data again - should include new user
        $result2 = $this->cacheService->getTopUsers(10);
        $this->assertCount(6, $result2);
        $this->assertEquals($newUser->id, $result2->first()->id);
    }

    /** @test */
    public function it_handles_cache_tags_correctly()
    {
        Cache::tags(['users', 'posts'])->put('user.1.posts', ['post1', 'post2'], 3600);
        Cache::tags(['users'])->put('user.1.profile', ['name' => 'John'], 3600);
        Cache::tags(['posts'])->put('recent.posts', ['post3', 'post4'], 3600);
        
        // Flush only user-tagged cache
        Cache::tags(['users'])->flush();
        
        $this->assertNull(Cache::tags(['users', 'posts'])->get('user.1.posts'));
        $this->assertNull(Cache::tags(['users'])->get('user.1.profile'));
        $this->assertNotNull(Cache::tags(['posts'])->get('recent.posts'));
    }

    /** @test */
    public function it_implements_cache_warming()
    {
        $users = User::factory()->count(50)->create();
        
        // Warm the cache
        $this->cacheService->warmCache();
        
        // Verify cache is populated
        $this->assertTrue(Cache::has('top_users'));
        $this->assertTrue(Cache::has('user_stats'));
        $this->assertTrue(Cache::has('popular_categories'));
        
        // Verify data is correct
        $cachedUsers = Cache::get('top_users');
        $this->assertCount(10, $cachedUsers);
    }

    /** @test */
    public function it_handles_redis_connection_failure_gracefully()
    {
        // Simulate Redis being down
        Redis::shouldReceive('connection->get')->andThrow(new \Exception('Redis connection failed'));
        
        // Should fall back to database
        $result = $this->cacheService->getTopUsers(10);
        
        $this->assertNotNull($result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }
