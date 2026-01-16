<?php

/**
 * OpenAPI/Swagger Documentation for Commerce Core Backend API
 *
 * This file contains the OpenAPI 3.0 specification for the API.
 * It can be used with tools like:
 * - Swagger UI
 * - ReDoc
 * - Postman
 *
 * Integration with Laravel:
 * Install: composer require darkaonline/l5-swagger
 * Generate docs: php artisan l5-swagger:generate
 * Access at: /api/documentation
 */

return [
    'swagger' => '3.0.0',
    'info' => [
        'title' => 'Commerce Core Backend API',
        'description' => 'RESTful API for E-commerce Platform - Auth and Orders Management',
        'version' => '1.0.0',
        'contact' => [
            'name' => 'API Support',
            'email' => 'support@commerce-core.local',
        ],
        'license' => [
            'name' => 'MIT',
        ],
    ],
    'servers' => [
        [
            'url' => 'http://localhost:8000/api',
            'description' => 'Local Development Server',
            'variables' => [
                'protocol' => [
                    'default' => 'http',
                    'enum' => ['http', 'https'],
                ],
            ],
        ],
        [
            'url' => 'https://api.commerce-core.local/api',
            'description' => 'Production Server',
        ],
    ],
    'components' => [
        'securitySchemes' => [
            'BearerToken' => [
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
                'description' => 'JWT token obtained from login endpoint',
            ],
        ],
        'schemas' => [
            // ====================================================================
            // AUTH SCHEMAS
            // ====================================================================

            'RegisterRequest' => [
                'type' => 'object',
                'required' => ['email', 'password'],
                'properties' => [
                    'email' => [
                        'type' => 'string',
                        'format' => 'email',
                        'example' => 'john@example.com',
                        'description' => 'User email address',
                    ],
                    'password' => [
                        'type' => 'string',
                        'format' => 'password',
                        'minLength' => 8,
                        'example' => 'secure-password-123',
                        'description' => 'User password (min 8 characters)',
                    ],
                ],
            ],

            'LoginRequest' => [
                'type' => 'object',
                'required' => ['email', 'password'],
                'properties' => [
                    'email' => [
                        'type' => 'string',
                        'format' => 'email',
                        'example' => 'john@example.com',
                    ],
                    'password' => [
                        'type' => 'string',
                        'format' => 'password',
                        'example' => 'secure-password-123',
                    ],
                ],
            ],

            'RegisterResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'userId' => ['type' => 'integer', 'example' => 123],
                            'email' => ['type' => 'string', 'example' => 'john@example.com'],
                        ],
                    ],
                    'message' => ['type' => 'string', 'example' => 'User registered successfully'],
                ],
            ],

            'LoginResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'userId' => ['type' => 'integer', 'example' => 123],
                            'token' => ['type' => 'string', 'example' => 'eyJ0eXAiOiJKV1QiLCJhbGc...'],
                            'expiresIn' => ['type' => 'integer', 'example' => 3600],
                        ],
                    ],
                    'message' => ['type' => 'string', 'example' => 'Authentication successful'],
                ],
            ],

            // ====================================================================
            // ORDER SCHEMAS
            // ====================================================================

            'CreateOrderResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'orderId' => ['type' => 'integer', 'example' => 456],
                            'status' => ['type' => 'string', 'enum' => ['DRAFT', 'CONFIRMED', 'PAID', 'CANCELLED']],
                            'itemCount' => ['type' => 'integer', 'example' => 0],
                            'totalAmount' => ['type' => 'integer', 'example' => 0],
                        ],
                    ],
                    'message' => ['type' => 'string', 'example' => 'Order created successfully'],
                ],
            ],

            'AddItemRequest' => [
                'type' => 'object',
                'required' => ['productId', 'quantity', 'unitPrice'],
                'properties' => [
                    'productId' => [
                        'type' => 'integer',
                        'minimum' => 1,
                        'example' => 789,
                        'description' => 'Product ID',
                    ],
                    'quantity' => [
                        'type' => 'integer',
                        'minimum' => 1,
                        'example' => 2,
                        'description' => 'Quantity to add',
                    ],
                    'unitPrice' => [
                        'type' => 'integer',
                        'minimum' => 1,
                        'example' => 2999,
                        'description' => 'Price per unit in cents',
                    ],
                ],
            ],

            'AddItemResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'orderId' => ['type' => 'integer', 'example' => 456],
                            'itemCount' => ['type' => 'integer', 'example' => 1],
                            'totalAmount' => ['type' => 'integer', 'example' => 5998],
                        ],
                    ],
                    'message' => ['type' => 'string', 'example' => 'Item added to order'],
                ],
            ],

            'ConfirmOrderResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'orderId' => ['type' => 'integer', 'example' => 456],
                            'status' => ['type' => 'string', 'example' => 'CONFIRMED'],
                            'itemCount' => ['type' => 'integer', 'example' => 2],
                            'totalAmount' => ['type' => 'integer', 'example' => 5998],
                            'formattedTotal' => ['type' => 'string', 'example' => '$59.98'],
                        ],
                    ],
                    'message' => ['type' => 'string', 'example' => 'Order confirmed successfully'],
                ],
            ],

            'OrderItem' => [
                'type' => 'object',
                'properties' => [
                    'productId' => ['type' => 'integer', 'example' => 789],
                    'quantity' => ['type' => 'integer', 'example' => 2],
                    'unitPrice' => ['type' => 'integer', 'example' => 2999],
                    'total' => ['type' => 'integer', 'example' => 5998],
                ],
            ],

            'OrderDetailsResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => [
                        'type' => 'object',
                        'properties' => [
                            'orderId' => ['type' => 'integer', 'example' => 456],
                            'userId' => ['type' => 'integer', 'example' => 123],
                            'status' => ['type' => 'string', 'example' => 'CONFIRMED'],
                            'itemCount' => ['type' => 'integer', 'example' => 2],
                            'items' => [
                                'type' => 'array',
                                'items' => ['$ref' => '#/components/schemas/OrderItem'],
                            ],
                            'totalAmount' => ['type' => 'integer', 'example' => 5998],
                        ],
                    ],
                ],
            ],

            // ====================================================================
            // ERROR SCHEMAS
            // ====================================================================

            'ErrorResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => false],
                    'error' => ['type' => 'string', 'example' => 'Email already exists'],
                    'code' => ['type' => 'string', 'example' => 'USER_ALREADY_EXISTS'],
                ],
            ],

            'ValidationErrorResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => false],
                    'error' => ['type' => 'string', 'example' => 'The email field is required'],
                    'code' => ['type' => 'string', 'example' => 'INVALID_INPUT'],
                ],
            ],

            'UnauthorizedResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => false],
                    'error' => ['type' => 'string', 'example' => 'Invalid email or password'],
                    'code' => ['type' => 'string', 'example' => 'INVALID_CREDENTIALS'],
                ],
            ],

            'NotFoundResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => false],
                    'error' => ['type' => 'string', 'example' => 'Order not found'],
                    'code' => ['type' => 'string', 'example' => 'NOT_FOUND'],
                ],
            ],
        ],
    ],
    'paths' => [
        '/auth/register' => [
            'post' => [
                'tags' => ['Authentication'],
                'summary' => 'Register a new user',
                'description' => 'Create a new user account with email and password',
                'operationId' => 'registerUser',
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => ['$ref' => '#/components/schemas/RegisterRequest'],
                            'examples' => [
                                'basicExample' => [
                                    'summary' => 'Basic registration',
                                    'value' => [
                                        'email' => 'john@example.com',
                                        'password' => 'secure-password-123',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'responses' => [
                    '201' => [
                        'description' => 'User registered successfully',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/RegisterResponse'],
                            ],
                        ],
                    ],
                    '409' => [
                        'description' => 'Email already exists',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/ErrorResponse'],
                            ],
                        ],
                    ],
                    '422' => [
                        'description' => 'Invalid input data',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/ValidationErrorResponse'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        '/auth/login' => [
            'post' => [
                'tags' => ['Authentication'],
                'summary' => 'Authenticate user',
                'description' => 'Login with email and password to get authentication token',
                'operationId' => 'loginUser',
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => ['$ref' => '#/components/schemas/LoginRequest'],
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Authentication successful',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/LoginResponse'],
                            ],
                        ],
                    ],
                    '401' => [
                        'description' => 'Invalid credentials',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/UnauthorizedResponse'],
                            ],
                        ],
                    ],
                    '422' => [
                        'description' => 'Invalid input data',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/ValidationErrorResponse'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        '/orders' => [
            'post' => [
                'tags' => ['Orders'],
                'summary' => 'Create new order',
                'description' => 'Create a new order for the authenticated user',
                'operationId' => 'createOrder',
                'security' => [
                    ['BearerToken' => []],
                ],
                'responses' => [
                    '201' => [
                        'description' => 'Order created successfully',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/CreateOrderResponse'],
                            ],
                        ],
                    ],
                    '401' => [
                        'description' => 'Unauthorized',
                    ],
                ],
            ],
        ],

        '/orders/{orderId}' => [
            'get' => [
                'tags' => ['Orders'],
                'summary' => 'Get order details',
                'description' => 'Retrieve detailed information about a specific order',
                'operationId' => 'getOrderDetails',
                'security' => [
                    ['BearerToken' => []],
                ],
                'parameters' => [
                    [
                        'name' => 'orderId',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer'],
                        'description' => 'Order ID',
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Order details retrieved',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/OrderDetailsResponse'],
                            ],
                        ],
                    ],
                    '401' => [
                        'description' => 'Unauthorized',
                    ],
                    '404' => [
                        'description' => 'Order not found',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/NotFoundResponse'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        '/orders/{orderId}/items' => [
            'post' => [
                'tags' => ['Orders'],
                'summary' => 'Add item to order',
                'description' => 'Add a product item to an existing order',
                'operationId' => 'addItemToOrder',
                'security' => [
                    ['BearerToken' => []],
                ],
                'parameters' => [
                    [
                        'name' => 'orderId',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer'],
                    ],
                ],
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => ['$ref' => '#/components/schemas/AddItemRequest'],
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Item added successfully',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/AddItemResponse'],
                            ],
                        ],
                    ],
                    '401' => [
                        'description' => 'Unauthorized',
                    ],
                    '404' => [
                        'description' => 'Order not found',
                    ],
                    '409' => [
                        'description' => 'Order already confirmed',
                    ],
                    '422' => [
                        'description' => 'Invalid item data',
                    ],
                ],
            ],
        ],

        '/orders/{orderId}/confirm' => [
            'post' => [
                'tags' => ['Orders'],
                'summary' => 'Confirm order',
                'description' => 'Confirm and finalize an order',
                'operationId' => 'confirmOrder',
                'security' => [
                    ['BearerToken' => []],
                ],
                'parameters' => [
                    [
                        'name' => 'orderId',
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'integer'],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Order confirmed successfully',
                        'content' => [
                            'application/json' => [
                                'schema' => ['$ref' => '#/components/schemas/ConfirmOrderResponse'],
                            ],
                        ],
                    ],
                    '401' => [
                        'description' => 'Unauthorized',
                    ],
                    '404' => [
                        'description' => 'Order not found',
                    ],
                    '409' => [
                        'description' => 'Order already confirmed',
                    ],
                    '422' => [
                        'description' => 'Order has no items',
                    ],
                ],
            ],
        ],
    ],
];
