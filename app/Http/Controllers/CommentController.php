<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CommentStoreRequest;
use App\Http\Requests\Comment\CommentUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    #[OA\Get(
        path: '/api/comments',
        description: 'Возвращает полный список комментариев в системе.',
        summary: 'Получить список комментариев',
        security: ['bearerAuth'],
        tags: ['Комментарии'], responses: [
            new OA\Response(
                response: 200,
                description: 'Список комментариев',
                content: new OA\JsonContent(ref: '#/components/schemas/CommentResource')
            )
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return CommentResource::collection(Comment::paginate());
    }

    #[OA\Post(
        path: '/api/comments',
        description: 'Создаёт новый комментарий на основе переданных данных.',
        summary: 'Создать комментарий',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CommentStoreRequest')
        ),
        tags: ['Комментарии'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Комментарий успешно создан',
                content: new OA\JsonContent(ref: '#/components/schemas/CommentResource')
            )
        ]
    )]
    public function store(CommentStoreRequest $request): CommentResource
    {
        return CommentResource::make(Comment::create($request->validated()));
    }

    #[OA\Get(
        path: '/api/comments/{id}',
        description: 'Возвращает данные комментария по его уникальному идентификатору.',
        summary: 'Получить комментарий по ID',
        tags: ['Комментарии'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID комментария',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Данные комментария',
                content: new OA\JsonContent(ref: '#/components/schemas/CommentResource')
            ),
            new OA\Response(response: 404, description: 'Комментарий не найден')
        ]
    )]
    public function show(Comment $comment): CommentResource
    {
        return CommentResource::make($comment);
    }

    #[OA\Put(
        path: '/api/comments/{id}',
        description: 'Обновляет данные комментария по его уникальному идентификатору.',
        summary: 'Обновить комментарий',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CommentUpdateRequest')
        ),
        tags: ['Комментарии'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID комментария',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Комментарий успешно обновлён',
                content: new OA\JsonContent(ref: '#/components/schemas/CommentResource')
            ),
            new OA\Response(response: 404, description: 'Комментарий не найден')
        ]
    )]
    public function update(CommentUpdateRequest $request, Comment $comment): CommentResource
    {
        $comment->update($request->validated());

        return CommentResource::make($comment);
    }

    #[OA\Delete(
        path: '/api/comments/{id}',
        description: 'Удаляет комментарий по его уникальному идентификатору.',
        summary: 'Удалить комментарий',
        tags: ['Комментарии'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID комментария',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'Комментарий успешно удалён'),
            new OA\Response(response: 404, description: 'Комментарий не найден')
        ]
    )]
    public function destroy(Comment $comment): \Illuminate\Http\Response
    {
        $comment->delete();

        return response()->noContent();
    }
}
