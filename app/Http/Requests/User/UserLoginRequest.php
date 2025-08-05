<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        new OA\Property(
            property: 'email',
            type: 'string',
        ),
        new OA\Property(
            property: 'password',
            type: 'string',
        ),
    ]
)]
class UserLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email',Rule::exists(User::class, 'email')],
            'password' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
