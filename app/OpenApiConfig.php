<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: 'Commerce Core Backend API',
        version: '1.0.0',
        description: 'Commerce Core Backend - Auth & Orders Management System',
        contact: new OA\Contact(
            name: 'API Support',
            email: 'support@example.com'
        )
    ),
    servers: [
        new OA\Server(
            url: 'http://localhost:8000',
            description: 'Development Server'
        ),
        new OA\Server(
            url: 'https://api.example.com',
            description: 'Production Server'
        ),
    ],
    components: new OA\Components(
        securitySchemes: [
            'bearerAuth' => new OA\SecurityScheme(
                description: 'JWT Bearer token',
                type: 'http',
                scheme: 'bearer',
                bearerFormat: 'JWT',
            ),
        ],
    ),
    tags: [
        new OA\Tag(
            name: 'Authentication',
            description: 'User registration and authentication'
        ),
        new OA\Tag(
            name: 'Orders',
            description: 'Order management'
        ),
    ],
)]
class OpenApiConfig {}
