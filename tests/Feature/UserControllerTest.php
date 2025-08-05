<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->actingAsUser();

    }
    public function test_index_returns_users_list(): void
    {
        User::factory()->count(3)->create();
        $response = $this->getJson('/api/users');
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_store_creates_user(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'V]n<4.apQ',
        ];
        $response = $this->postJson('/api/users', $data);
        $response->assertStatus(201)
            ->assertJsonStructure(['data']);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_show_returns_user(): void
    {
        $user = User::factory()->create();
        $response = $this->getJson("/api/users/{$user->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_update_modifies_user(): void
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'Updated Name',
            'email' => $user->email,
        ];
        $response = $this->putJson("/api/users/{$user->id}", $data);
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_destroy_deletes_user(): void
    {
        $user = User::factory()->create();
        $response = $this->deleteJson("/api/users/{$user->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_posts_returns_user_posts(): void
    {
        $user = User::factory()->create();
        $response = $this->getJson("/api/users/{$user->id}/posts");
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_comments_returns_user_comments(): void
    {
        $user = User::factory()->hasComments(3)->create();
        $response = $this->getJson("/api/users/{$user->id}/comments");
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_register_creates_user_and_returns_201(): void
    {
        $data = [
            'name' => 'Reg User',
            'email' => 'reg@example.com',
            'password' => 'V]n<4.apQ',
        ];
        $response = $this->postJson('/api/auth/register', $data);
        $response->assertStatus(201)
            ->assertJsonStructure(['data']);
        $this->assertDatabaseHas('users', ['email' => 'reg@example.com']);
    }

    public function test_login_returns_token_on_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'V]n<4.apQ',
        ]);
        $data = [
            'email' => 'login@example.com',
            'password' => 'V]n<4.apQ',
        ];
        $response = $this->postJson('/api/auth/login', $data);
        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'fail@example.com',
            'password' => bcrypt('correct-password'),
        ]);
        $data = [
            'email' => 'fail@example.com',
            'password' => 'wrong-password',
        ];
        $response = $this->postJson('/api/auth/login', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }
}
