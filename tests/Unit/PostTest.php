<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_can_be_created()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Post',
            'body' => 'Test Body',
        ]);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('Test Post', $post->title);
        $this->assertEquals('Test Body', $post->body);
        $this->assertEquals($user->id, $post->user_id);
    }

    public function test_post_belongs_to_user()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($user->id, $post->user->id);
    }
}
