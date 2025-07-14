<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
//}


// namespace Tests\Feature;

// use Tests\TestCase;
// use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;

// class LoginTest extends TestCase
// {
    use RefreshDatabase;

    public function test_login_page_loads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

public function test_user_can_login_with_valid_credentials()
{
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this
        ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
        ->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

    $response->assertRedirect('dashboard');
    $this->assertAuthenticatedAs($user);
}


    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $response = $this
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
        ->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',

        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }




}
