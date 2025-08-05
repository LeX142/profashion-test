<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function getListQuery(array $filters = []): Builder
    {
        return User::query()
            ->when(
                $filters['name'] ?? null,
                fn(Builder $query, string $name) => $query->where('name', 'like', "%$name%")
            )
            ->when(
                $filters['email'] ?? null,
                fn(Builder $query, string $email) => $query->where('email', 'like', "%$email%")
            );
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

    public function loginUser(array $data): ?string
    {
        if (\Arr::hasAll($data,['email', 'password'])) {
            $user = User::where('email', \Arr::get($data, 'email'))->first();

            if (!$user || !Hash::check(\Arr::get($data, 'password'), $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            return $user->createToken('api')->plainTextToken;
        }

        return null;
    }
}
