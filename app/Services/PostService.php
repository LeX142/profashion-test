<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class PostService
{
    private function getRelations(): array
    {
        return [
            'user:id,name,email'
        ];
    }

    public function getListQuery(array $filters = [])
    {
        return Post::query()
            ->when(
                $filters['title'] ?? null,
                fn(Builder $query, string $title) => $query->where('title', 'like', "%$title%")
            )
            ->when(
                $filters['body'] ?? null,
                fn(Builder $query, string $body) => $query->where('body', 'like', "%$body%")
            )
            ->when(
                $filters['user_id'] ?? null,
                fn(Builder $query, int $userId) => $query->where('user_id', $userId)
            )
            ->when(
                $filters['with_comments'] ?? null,
                fn(Builder $query, $withComments) => filter_var($withComments, FILTER_VALIDATE_BOOLEAN)
                    ? $query->whereHas('comments')
                    : $query->doesntHave('comments')
            )
            ->with($this->getRelations())
            ->orderByDesc('created_at');
    }

    public function getPost(int $postId): Post
    {
        return Post::query()->whereKey($postId)
            ->with($this->getRelations())
            ->firstOrFail();
    }

    public function createPost(array $data): Post
    {
        return Post::create($data)->loadMissing($this->getRelations());
    }

    public function updatePost(Post $post, array $data): Post
    {
        $post->update(\Arr::only($data, ['title', 'body']));
        $post->loadMissing($this->getRelations());

        return $post;
    }

    public function getPostCommentsQuery(Post $post)
    {
        return $post->comments()->with($this->getRelations());
    }
}
