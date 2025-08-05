<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/** @mixin Post */
#[OA\Schema(
    schema: 'PostResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'body', type: 'string'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'user_id', type: 'integer'),
        new OA\Property(
            property: 'user',
            ref: '#/components/schemas/UserResource',
            nullable: true
        ),
        new OA\Property(
            property: 'comments',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/CommentResource'),
            nullable: true
        ),
    ]
)]
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
