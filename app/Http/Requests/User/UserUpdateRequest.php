<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        new OA\Property(property: 'name', description: 'Имя пользователя', type: 'string'),
        new OA\Property(property: 'email', description: 'Email пользователя', type: 'string'),
    ]
)]
class UserUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('user'))
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
