<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/** @mixin Comment */
#[OA\Schema(
    schema: 'CommentResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'body', type: 'string'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'user_id', type: 'integer'),
        new OA\Property(property: 'post_id', type: 'integer'),
        new OA\Property(
            property: 'post',
            ref: '#/components/schemas/PostResource',
            nullable: true
        ),
        new OA\Property(
            property: 'user',
            ref: '#/components/schemas/UserResource',
            nullable: true
        ),
    ]
)]
class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user_id' => $this->user_id,
            'post_id' => $this->post_id,

            'post' => PostResource::make($this->whenLoaded('post')),
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
