<?php

namespace Tests\Integration\Database;

use App\Models\User;
use App\Models\Order;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    /** @test */
    public function it_finds_users_with_orders_in_date_range()
    {
        // Create test data
        $userWithOrders = User::factory()->create();
        $userWithoutOrders = User::factory()->create();
        
        Order::factory()->create([
            'user_id' => $userWithOrders->id,
            'created_at' => now()->subDays(5)
        ]);
        
        Order::factory()->create([
            'user_id' => $userWithOrders->id,
            'created_at' => now()->subDays(15)
        ]);

        // Test the repository method
        $startDate = now()->subDays(10);
        $endDate = now();
        
        $users = $this->repository->findUsersWithOrdersInDateRange($startDate, $endDate);

        $this->assertCount(1, $users);
        $this->assertEquals($userWithOrders->id, $users->first()->id);
    }

    /** @test */
    public function it_calculates_user_lifetime_value()
    {
        $user = User::factory()->create();
        
        Order::factory()->create([
            'user_id' => $user->id,
            'total' => 100.50,
            'status' => 'completed'
        ]);
        
        Order::factory()->create([
            'user_id' => $user->id,
            'total' => 50.25,
            'status' => 'completed'
        ]);
        
        Order::factory()->create([
            'user_id' => $user->id,
            'total' => 75.00,
            'status' => 'cancelled'
        ]);

        $ltv = $this->repository->calculateLifetimeValue($user->id);

        $this->assertEquals(150.75, $ltv);
    }

    /** @test */
    public function it_bulk_updates_user_statuses()
    {
        $users = User::factory()->count(5)->create(['status' => 'active']);
        $userIds = $users->pluck('id')->toArray();

        $this->repository->bulkUpdateStatus($userIds, 'inactive');

        foreach ($userIds as $userId) {
            $this->assertDatabaseHas('users', [
                'id' => $userId,
                'status' => 'inactive'
            ]);
        }
    }

    /** @test */
    public function it_uses_transactions_for_complex_operations()
    {
        $this->expectException(\Exception::class);

        try {
            $this->repository->createUserWithProfile([
                'user' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'password' => 'password'
                ],
                'profile' => [
                    'bio' => 'Test bio',
                    'invalid_field' => 'This will cause an error'
                ]
            ]);
        } catch (\Exception $e) {
            // Verify transaction was rolled back
            $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
            throw $e;
        }
    }

    /** @test */
    public function it_efficiently_queries_with_relationships()
    {
        $users = User::factory()->count(10)->create();
        
        foreach ($users as $user) {
            Order::factory()->count(5)->create(['user_id' => $user->id]);
        }

        DB::enableQueryLog();
        
        $result = $this->repository->getUsersWithOrders();
        
        $queries = DB::getQueryLog();
        
        // Should only have 2 queries (users + orders), not N+1
        $this->assertCount(2, $queries);
        $this->assertCount(10, $result);
        $this->assertTrue($result->first()->relationLoaded('orders'));
    }
}