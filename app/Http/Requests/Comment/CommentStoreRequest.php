<?php

declare(strict_types=1);

namespace App\Http\Requests\Comment;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        new OA\Property(property: 'post_id', description: 'ID поста', type: 'integer'),
        new OA\Property(property: 'body', description: 'Текст комментария', type: 'string'),
    ]
)]
class CommentStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', Rule::exists(User::class, 'id')],
            'post_id' => ['required', Rule::exists(Post::class, 'id')],
            'body' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
           'user_id' => $this->user()->id
        ]);
    }
}
