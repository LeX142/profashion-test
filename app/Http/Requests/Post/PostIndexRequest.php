<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'user_id' => ['nullable', Rule::exists(User::class, 'id')],
            'title' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'with_comments' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
