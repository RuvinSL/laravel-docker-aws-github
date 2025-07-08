<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Events\UserCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $service;
    private $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockRepository = Mockery::mock(UserRepository::class);
        $this->service = new UserService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_creates_a_user_with_hashed_password()
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $expectedUser = new User([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->withArgs(function ($data) {
                return $data['name'] === 'John Doe' 
                    && $data['email'] === 'john@example.com'
                    && Hash::check('password123', $data['password']);
            })
            ->andReturn($expectedUser);

        Event::fake();

        // Act
        $result = $this->service->createUser($userData);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('john@example.com', $result->email);
        Event::assertDispatched(UserCreated::class);
    }

    /** @test */
    public function it_throws_exception_for_duplicate_email()
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ];

        $this->mockRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('existing@example.com')
            ->andReturn(new User());

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email already exists');
        
        $this->service->createUser($userData);
    }

    /** @test */
    public function it_calculates_user_statistics_correctly()
    {
        // Arrange
        $userId = 1;
        $orders = collect([
            ['total' => 100.00, 'status' => 'completed'],
            ['total' => 50.00, 'status' => 'completed'],
            ['total' => 75.00, 'status' => 'pending'],
        ]);

        $this->mockRepository
            ->shouldReceive('getUserOrders')
            ->once()
            ->with($userId)
            ->andReturn($orders);

        // Act
        $stats = $this->service->getUserStatistics($userId);

        // Assert
        $this->assertEquals(150.00, $stats['total_spent']);
        $this->assertEquals(2, $stats['completed_orders']);
        $this->assertEquals(75.00, $stats['average_order_value']);
    }
}