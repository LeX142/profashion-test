<?php

declare(strict_types=1);

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        new OA\Property(property: 'body', description: 'Тело комментария', type: 'string'),
    ]
)]
class CommentUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
