<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['name', 'email', 'password'];
        $user = new User();
        
        $this->assertEquals($fillable, $user->getFillable());
    }

    /** @test */
    public function it_hides_sensitive_attributes()
    {
        $hidden = ['password', 'remember_token'];
        $user = new User();
        
        $this->assertEquals($hidden, $user->getHidden());
    }

    /** @test */
    public function it_casts_email_verified_at_to_datetime()
    {
        $user = User::factory()->create([
            'email_verified_at' => '2023-01-01 00:00:00'
        ]);
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verified_at);
    }

    /** @test */
    public function it_can_determine_if_user_has_role()
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $userRole = Role::factory()->create(['name' => 'user']);
        
        $user->roles()->attach($adminRole);
        
        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('user'));
        $this->assertFalse($user->hasRole('moderator'));
    }

    /** @test */
    public function it_calculates_full_name_attribute()
    {
        $user = new User([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
        
        $this->assertEquals('John Doe', $user->full_name);
    }

    /** @test */
    public function it_scopes_active_users()
    {
        User::factory()->count(3)->create(['active' => true]);
        User::factory()->count(2)->create(['active' => false]);
        
        $this->assertEquals(3, User::active()->count());
    }
}