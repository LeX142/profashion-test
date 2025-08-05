<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page'=>['nullable','integer'],
            'per_page'=>['nullable','integer'],
            'name' => ['nullable','string'],
            'email' => ['nullable','email'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
