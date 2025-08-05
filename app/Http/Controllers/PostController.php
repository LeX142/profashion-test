<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Post\PostIndexRequest;
use App\Http\Requests\Post\PostStoreRequest;
use App\Http\Requests\Post\PostUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class PostController extends Controller
{
    public function __construct(protected PostService $service)
    {
    }

    #[OA\Get(
        path: '/api/posts',
        description: 'Возвращает полный список постов в системе.',
        summary: 'Получить список постов',
        security: ['bearerAuth'],
        tags: ['Посты'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Номер страницы',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'per_page',
                description: 'Количество постов на странице',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'title',
                description: 'Поиск по заголовку поста',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'body',
                description: 'Поиск по содержанию поста',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'user_id',
                description: 'Поиск по автору поста',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'with_comments',
                description: 'Поиск постов с комментариями',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список постов',
                content: new OA\JsonContent(allOf: [
                    new OA\Schema(properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/PostResource')
                        )
                    ]),
                    new OA\Schema(ref: '#/components/schemas/laravel-pagination'),
                ])
            )
        ]
    )]
    public function index(PostIndexRequest $request): AnonymousResourceCollection
    {
        $postsQuery = $this->service->getListQuery($request->validated());

        return PostResource::collection($postsQuery->paginate());
    }

    #[OA\Post(
        path: '/api/posts',
        description: 'Создаёт новый пост на основе переданных данных.',
        summary: 'Создать пост',
        security: ['bearerAuth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PostStoreRequest')
        ),
        tags: ['Посты'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Пост успешно создан',
                content: new OA\JsonContent(ref: '#/components/schemas/PostResource')
            )
        ]
    )]
    public function store(PostStoreRequest $request): PostResource
    {
        $post = $this->service->createPost($request->validated());
        return PostResource::make($post);
    }

    #[OA\Get(
        path: '/api/posts/{id}',
        description: 'Возвращает данные поста по его уникальному идентификатору.',
        summary: 'Получить пост по ID',
        security: ['bearerAuth'],
        tags: ['Посты'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID поста',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Данные поста',
                content: new OA\JsonContent(ref: '#/components/schemas/PostResource')
            ),
            new OA\Response(response: 404, description: 'Пост не найден')
        ]
    )]
    public function show(FormRequest $request): PostResource
    {
        $post = $this->service->getPost((int)$request->route('post'));

        return PostResource::make($post);
    }

    #[OA\Put(
        path: '/api/posts/{id}',
        description: 'Обновляет данные поста по его уникальному идентификатору.',
        summary: 'Обновить пост',
        security: ['bearerAuth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PostUpdateRequest')
        ),
        tags: ['Посты'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID поста',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Пост успешно обновлён',
                content: new OA\JsonContent(ref: '#/components/schemas/PostResource')
            ),
            new OA\Response(response: 404, description: 'Пост не найден')
        ]
    )]
    public function update(PostUpdateRequest $request, Post $post): PostResource
    {
        $post = $this->service->updatePost($post, $request->validated());

        return PostResource::make($post);
    }

    #[OA\Delete(
        path: '/api/posts/{id}',
        description: 'Удаляет пост по его уникальному идентификатору.',
        summary: 'Удалить пост',
        security: ['bearerAuth'],
        tags: ['Посты'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID поста',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'Пост успешно удалён'),
            new OA\Response(response: 404, description: 'Пост не найден')
        ]
    )]
    public function destroy(Post $post): Response
    {
        $post->delete();

        return response()->noContent();
    }

    #[OA\Get(
        path: '/api/posts/{id}/comments',
        description: 'Возвращает комментарии поста по его уникальному идентификатору.',
        summary: 'Получить комментарии поста',
        security: ['bearerAuth'],
        tags: ['Комментарии'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID поста',
                in: 'path',
                required: true,
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Номер страницы',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'per_page',
                description: 'Количество комментариев на странице',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Комментарии поста',
                content: new OA\JsonContent(allOf: [
                    new OA\Schema(properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/CommentResource')
                        )
                    ]),
                    new OA\Schema(ref: '#/components/schemas/laravel-pagination'),
                ])
            ),
            new OA\Response(response: 404, description: 'Пост не найден')
        ]

    )]
    public function comments(Post $post): AnonymousResourceCollection
    {
        $commentsQuery = $this->service->getPostCommentsQuery($post);

        return CommentResource::collection($commentsQuery->paginate());
    }
}
