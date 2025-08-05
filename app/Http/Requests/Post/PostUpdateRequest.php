<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
       new OA\Property(property: 'title', description: 'Заголовок поста', type: 'string'),
       new OA\Property(property: 'body', description: 'Тело поста', type: 'string'),
    ]
)]
class PostUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:4096'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
