<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\OpenApi(openapi: '3.1.0')]
#[OA\Info(
    version: '1.0.0',
    description: 'ProFashion OpenApi',
    title: 'ProFashion OpenApi',
    contact: new OA\Contact(
        name: 'ProFashion',
        url: 'https://profashion.ru'
    )
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    bearerFormat: 'JWT',
    scheme: 'bearer'
)]
#[OA\Server(
    url: 'http://localhost:8988',
    description: 'Local server'
)]
#[OA\Schema(schema: 'laravel-pagination', properties: [
    new OA\Property(property: 'links', properties: [
        new OA\Property(property: 'first', type: 'string'),
        new OA\Property(property: 'last', type: 'string'),
        new OA\Property(property: 'prev', type: 'string'),
        new OA\Property(property: 'next', type: 'string'),
    ]),
    new OA\Property(property: 'meta', properties: [
        new OA\Property(property: 'current_page', type: 'integer'),
        new OA\Property(property: 'from', type: 'integer'),
        new OA\Property(property: 'last_page', type: 'integer'),
        new OA\Property(property: 'links', additionalProperties: new OA\AdditionalProperties(
            properties: [
                new OA\Property(property: 'url', type: 'string'),
                new OA\Property(property: 'label', type: 'string'),
                new OA\Property(property: 'active', type: 'boolean'),
            ]
        )),
        new OA\Property(property: 'path', type: 'string'),
        new OA\Property(property: 'per_page', type: 'integer'),
        new OA\Property(property: 'to', type: 'integer'),
        new OA\Property(property: 'total', type: 'string'),
    ]),
])]

abstract class Controller
{
    //
}
