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
        new OA\Property(property: 'password', description: 'Пароль пользователя', type: 'string'),
    ]
)]
class UserStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => [
                'required',
                'string',
                $this->isPrecognitive()
                    ? Password::min(8)
                    : Password::min(8)->uncompromised()
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
