<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    private PostService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostService();
    }

    public function test_get_list_query_with_filters(): void
    {
        $user = User::factory()->create();
        Post::factory()->create(['title' => 'Hello World', 'body' => 'Body text', 'user_id' => $user->id]);
        Post::factory()->create(['title' => 'Another', 'body' => 'Other body', 'user_id' => $user->id]);

        $query = $this->service->getListQuery(['title' => 'Hello']);
        $this->assertEquals(1, $query->count());
        $this->assertEquals('Hello World', $query->first()->title);
    }

    public function test_get_post_by_id_and_model(): void
    {
        $post = Post::factory()->create();
        $foundById = $this->service->getPost($post->id);
        $foundByModel = $this->service->getPost($post);
        $this->assertEquals($post->id, $foundById->id);
        $this->assertEquals($post->id, $foundByModel->id);
    }

    public function test_create_post(): void
    {
        $user = User::factory()->create();
        $data = [
            'title' => 'Test Post',
            'body' => 'Test body',
            'user_id' => $user->id,
        ];
        $post = $this->service->createPost($data);
        $this->assertDatabaseHas('posts', ['title' => 'Test Post', 'body' => 'Test body', 'user_id' => $user->id]);
        $this->assertEquals('Test Post', $post->title);
    }

    public function test_update_post(): void
    {
        $post = Post::factory()->create(['title' => 'Old', 'body' => 'Old body']);
        $updated = $this->service->updatePost($post, ['title' => 'New', 'body' => 'New body']);
        $this->assertEquals('New', $updated->title);
        $this->assertEquals('New body', $updated->body);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'New', 'body' => 'New body']);
    }
}
