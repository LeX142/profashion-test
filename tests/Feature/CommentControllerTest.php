<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }

    public function test_index_returns_comments_list(): void
    {
        Comment::factory()->count(3)->create();
        $response = $this->getJson('/api/comments');
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_store_creates_comment(): void
    {
        $post = Post::factory()->create();
        $data = [
            'body' => 'Test comment',
            'post_id' => $post->id
        ];
        $response = $this->postJson('/api/comments', $data);
        $response->assertStatus(201)
            ->assertJsonStructure(['data']);
        $this->assertDatabaseHas('comments', ['post_id' => $post->id, 'body' => 'Test comment']);
    }

    public function test_show_returns_comment(): void
    {
        $comment = Comment::factory()->create();
        $response = $this->getJson("/api/comments/{$comment->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_update_modifies_comment(): void
    {
        $comment = Comment::factory()->create();
        $data = [
            'body' => 'Updated comment',
            // Добавьте другие необходимые поля
        ];
        $response = $this->putJson("/api/comments/{$comment->id}", $data);
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'body' => 'Updated comment']);
    }

    public function test_destroy_deletes_comment(): void
    {
        $comment = Comment::factory()->create();
        $response = $this->deleteJson("/api/comments/{$comment->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
