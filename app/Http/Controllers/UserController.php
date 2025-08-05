<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\UserIndexRequest;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
    }

    #[OA\Get(
        path: '/api/users',
        description: 'Возвращает полный список пользователей системы.',
        summary: 'Получить список пользователей',
        security: ['bearerAuth'],
        tags: ['Пользователи'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Номер страницы',
                in: 'query',
                required: false,
            ),
            new OA\Parameter(
                name: 'per_page',
                description: 'Количество пользователей на странице',
                in: 'query',
                required: false,
            ),
            new OA\Parameter(
                name: 'name',
                description: 'Поиск по имени пользователя',
                in: 'query',
                required: false,
            ),
            new OA\Parameter(
                name: 'email',
                description: 'Поиск по email пользователя',
                in: 'query',
                required: false,
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список пользователей',
                content: new OA\JsonContent(allOf: [
                    new OA\Schema(properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/UserResource')
                        )
                    ]),
                    new OA\Schema(ref: '#/components/schemas/laravel-pagination'),
                ])
            )
        ]
    )]
    public function index(UserIndexRequest $request): AnonymousResourceCollection
    {
        $userQuery = $this->service->getListQuery($request->validated());

        return UserResource::collection($userQuery->paginate());
    }

    #[OA\Post(
        path: '/api/users',
        description: 'Создаёт нового пользователя на основе переданных данных.',
        summary: 'Создать пользователя',
        security: ['bearerAuth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserStoreRequest')
        ),
        tags: ['Пользователи'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Пользователь успешно создан',
                content: new OA\JsonContent(ref: '#/components/schemas/UserResource')
            )
        ]
    )]
    public function store(UserStoreRequest $request): UserResource
    {
        $user = $this->service->createUser($request->validated());
        return UserResource::make($user);
    }

    #[OA\Get(
        path: '/api/users/{id}',
        description: 'Возвращает данные пользователя по его уникальному идентификатору.',
        summary: 'Получить пользователя по ID',
        security: ['bearerAuth'],
        tags: ['Пользователи'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID пользователя',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Данные пользователя',
                content: new OA\JsonContent(ref: '#/components/schemas/UserResource')
            ),
            new OA\Response(response: 404, description: 'Пользователь не найден')
        ]
    )]
    public function show(User $user): UserResource
    {
        return UserResource::make($user);
    }

    #[OA\Put(
        path: '/api/users/{id}',
        description: 'Обновляет данные пользователя по его уникальному идентификатору.',
        summary: 'Обновить пользователя',
        security: ['bearerAuth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserUpdateRequest')
        ),
        tags: ['Пользователи'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID пользователя',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Пользователь успешно обновлён',
                content: new OA\JsonContent(ref: '#/components/schemas/UserResource')
            ),
            new OA\Response(response: 404, description: 'Пользователь не найден')
        ]
    )]
    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return UserResource::make($user);
    }

    #[OA\Delete(
        path: '/api/users/{id}',
        description: 'Удаляет пользователя по его уникальному идентификатору.',
        summary: 'Удалить пользователя',
        security: ['bearerAuth'],
        tags: ['Пользователи'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID пользователя',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'Пользователь успешно удалён'),
            new OA\Response(response: 404, description: 'Пользователь не найден')
        ]
    )]
    public function destroy(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }

    #[OA\Get(
        path: '/api/users/{id}/posts',
        description: 'Возвращает список постов пользователя по его уникальному идентификатору.',
        summary: 'Получить посты пользователя',
        security: ['bearerAuth'],
        tags: ['Пользователи'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID пользователя',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Номер страницы с постами пользователя',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'per_page',
                description: 'Количество постов пользователя на странице',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список постов пользователя',
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
    public function posts(User $user): AnonymousResourceCollection
    {
        $posts = $user->posts()->paginate();

        return PostResource::collection($posts);
    }

    #[OA\Get(
        path: '/api/users/{id}/comments',
        description: 'Возвращает список комментариев пользователя по его уникальному идентификатору.',
        summary: 'Получить комментарии пользователя',
        security: ['bearerAuth'],
        tags: ['Пользователи'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID пользователя',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Номер страницы с комментариями пользователя',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'per_page',
                description: 'Количество комментариев пользователя на странице',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список комментариев пользователя',
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
            )
        ]
    )]
    public function comments(User $user): AnonymousResourceCollection
    {
        $comments = $user->comments()->paginate();

        return CommentResource::collection($comments);
    }

    #[OA\Post(
        path: '/api/auth/login',
        description: 'Авторизует пользователя на основе переданных данных.',
        summary: 'Авторизация',
        security: ['bearerAuth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserLoginRequest')
        ),
        tags: ['Аутентификация'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Пользователь успешно авторизован',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'token', type: 'string')
                ])
            ),
        ]
    )]
    public function login(UserLoginRequest $request): JsonResponse
    {
        if (!$token = $this->service->loginUser($request->validated())){
            abort(401);
        }

        return response()->json(['token' => $token]);
    }

    #[OA\Post(
        path: '/api/auth/register',
        description: 'Регистрирует нового пользователя на основе переданных данных.',
        summary: 'Регистрация',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserStoreRequest')
        ),
        tags: ['Аутентификация'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Пользователь успешно зарегистрирован',
                content: new OA\JsonContent(ref: '#/components/schemas/UserResource')
            )
        ]
    )]
    public function register(UserStoreRequest $request): UserResource
    {
        return $this->store($request);
    }
}
