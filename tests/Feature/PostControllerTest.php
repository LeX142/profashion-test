<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }

    public function test_index_returns_posts_list(): void
    {
        Post::factory()->count(3)->create();
        $response = $this->getJson('/api/posts');
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_store_creates_post(): void
    {
        $data = [
            'title' => 'Test Post',
            'body' => 'Test body',
        ];
        $response = $this->postJson('/api/posts', $data);
        $response->assertStatus(201)
            ->assertJsonStructure(['data']);
        $this->assertDatabaseHas('posts', ['title' => 'Test Post', 'body' => 'Test body']);
    }

    public function test_show_returns_post(): void
    {
        $post = Post::factory()->create();
        $response = $this->getJson("/api/posts/{$post->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_update_modifies_post(): void
    {
        $post = Post::factory()->create(['title' => 'Old', 'body' => 'Old body']);
        $data = [
            'title' => 'New',
            'body' => 'New body',
        ];
        $response = $this->putJson("/api/posts/{$post->id}", $data);
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'New', 'body' => 'New body']);
    }

    public function test_destroy_deletes_post(): void
    {
        $post = Post::factory()->create();
        $response = $this->deleteJson("/api/posts/{$post->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_comments_returns_post_comments(): void
    {
        $post = Post::factory()->hasComments(2)->create();
        $response = $this->getJson("/api/posts/{$post->id}/comments");
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }
}
