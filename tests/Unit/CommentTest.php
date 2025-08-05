<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_can_be_created()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'Test Comment',
        ]);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('Test Comment', $comment->body);
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($post->id, $comment->post_id);
    }

    public function test_comment_belongs_to_user()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
        $this->assertInstanceOf(User::class, $comment->user);
        $this->assertEquals($user->id, $comment->user->id);
    }

    public function test_comment_belongs_to_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
        $this->assertInstanceOf(Post::class, $comment->post);
        $this->assertEquals($post->id, $comment->post->id);
    }
}
