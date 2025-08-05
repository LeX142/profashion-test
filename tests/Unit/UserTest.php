<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('user@example.com', $user->email);
        $this->assertNotEmpty($user->password);
    }

    public function test_hidden_fields_are_hidden_in_array()
    {
        $user = User::factory()->make();
        $array = $user->toArray();
        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    public function test_casts()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }
}
