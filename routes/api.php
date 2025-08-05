<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
Route::get('docs', static function () {
    if (!file_exists(base_path('spec.yml'))) {
        abort(404);
    }
    return file_get_contents(base_path('spec.yml'));
});
Route::prefix('auth')->group(function () {
    Route::post('/register', [UserController::class, 'store']);
    Route::post('/login', [UserController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('/users/{user}/posts', [UserController::class, 'posts']);
    Route::get('/users/{user}/comments', [UserController::class, 'comments']);

    Route::apiResource('posts', PostController::class);
    Route::get('/posts/{post}/comments', [PostController::class, 'comments']);

    Route::apiResource('comments', CommentController::class);
});

