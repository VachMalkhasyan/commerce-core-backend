<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

/**
 * API Routes
 *
 * Base URL: /api
 *
 * AUTHENTICATION CONTEXT ROUTES
 * ============================
 *
 * POST   /auth/register          - Register a new user
 * POST   /auth/login             - Authenticate user and get token
 * POST   /auth/logout            - Logout (requires auth)
 * GET    /auth/me                - Get current user info (requires auth)
 *
 *
 * ORDERS CONTEXT ROUTES
 * =====================
 *
 * POST   /orders                 - Create a new order (requires auth)
 * GET    /orders/{orderId}       - Get order details (requires auth)
 * POST   /orders/{orderId}/items - Add item to order (requires auth)
 * POST   /orders/{orderId}/confirm - Confirm order (requires auth)
 */

// ============================================================================
// Public Auth Routes (No Authentication Required)
// ============================================================================

Route::prefix('auth')->group(function () {
    /**
     * POST /api/auth/register
     *
     * Register a new user.
     *
     * Request:
     * {
     *     "email": "john@example.com",
     *     "password": "secure-password-123"
     * }
     *
     * Response (201):
     * {
     *     "success": true,
     *     "data": {
     *         "userId": 123,
     *         "email": "john@example.com"
     *     },
     *     "message": "User registered successfully"
     * }
     *
     * Error Responses:
     * - 409: Email already exists
     * - 422: Invalid email or password format
     * - 500: Internal server error
     */
    Route::post('register', [AuthController::class, 'register'])
        ->name('auth.register');

    /**
     * POST /api/auth/login
     *
     * Authenticate user and get authentication token.
     *
     * Request:
     * {
     *     "email": "john@example.com",
     *     "password": "secure-password-123"
     * }
     *
     * Response (200):
     * {
     *     "success": true,
     *     "data": {
     *         "userId": 123,
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *         "expiresIn": 3600
     *     },
     *     "message": "Authentication successful"
     * }
     *
     * Error Responses:
     * - 401: Invalid credentials
     * - 422: Invalid input format
     * - 500: Internal server error
     */
    Route::post('login', [AuthController::class, 'login'])
        ->name('auth.login');
});

// ============================================================================
// Protected Routes (Authentication Required)
// ============================================================================

Route::middleware('auth:api')->group(function () {
    // ----
    // Orders Routes
    // ----

    /**
     * POST /api/orders
     *
     * Create a new order for the authenticated user.
     *
     * Headers:
     *     Authorization: Bearer {token}
     *
     * Response (201):
     * {
     *     "success": true,
     *     "data": {
     *         "orderId": 456,
     *         "status": "DRAFT",
     *         "itemCount": 0,
     *         "totalAmount": 0
     *     },
     *     "message": "Order created successfully"
     * }
     *
     * Error Responses:
     * - 401: User not authenticated
     * - 500: Internal server error
     */
    Route::post('orders', [OrderController::class, 'create'])
        ->name('orders.create');

    /**
     * GET /api/orders/{orderId}
     *
     * Retrieve order details by ID.
     *
     * Parameters:
     *     orderId (path) - Order ID
     *
     * Headers:
     *     Authorization: Bearer {token}
     *
     * Response (200):
     * {
     *     "success": true,
     *     "data": {
     *         "orderId": 456,
     *         "userId": 123,
     *         "status": "CONFIRMED",
     *         "itemCount": 2,
     *         "items": [
     *             {
     *                 "productId": 789,
     *                 "quantity": 2,
     *                 "unitPrice": 2999,
     *                 "total": 5998
     *             }
     *         ],
     *         "totalAmount": 5998
     *     }
     * }
     *
     * Error Responses:
     * - 401: User not authenticated
     * - 404: Order not found
     * - 500: Internal server error
     */
    Route::get('orders/{orderId}', [OrderController::class, 'show'])
        ->name('orders.show');

    /**
     * POST /api/orders/{orderId}/items
     *
     * Add an item to an existing order.
     *
     * Parameters:
     *     orderId (path) - Order ID
     *
     * Request:
     * {
     *     "productId": 789,
     *     "quantity": 2,
     *     "unitPrice": 2999
     * }
     *
     * Headers:
     *     Authorization: Bearer {token}
     *
     * Response (200):
     * {
     *     "success": true,
     *     "data": {
     *         "orderId": 456,
     *         "itemCount": 1,
     *         "totalAmount": 5998
     *     },
     *     "message": "Item added to order"
     * }
     *
     * Error Responses:
     * - 401: User not authenticated
     * - 404: Order not found
     * - 409: Order already confirmed
     * - 422: Invalid item data (quantity or price <= 0)
     * - 500: Internal server error
     */
    Route::post('orders/{orderId}/items', [OrderController::class, 'addItem'])
        ->name('orders.addItem');

    /**
     * POST /api/orders/{orderId}/confirm
     *
     * Confirm and finalize an order.
     *
     * Parameters:
     *     orderId (path) - Order ID to confirm
     *
     * Headers:
     *     Authorization: Bearer {token}
     *
     * Response (200):
     * {
     *     "success": true,
     *     "data": {
     *         "orderId": 456,
     *         "status": "CONFIRMED",
     *         "itemCount": 2,
     *         "totalAmount": 5998,
     *         "formattedTotal": "$59.98"
     *     },
     *     "message": "Order confirmed successfully"
     * }
     *
     * Error Responses:
     * - 401: User not authenticated
     * - 404: Order not found
     * - 409: Order already confirmed or in invalid state
     * - 422: Order has no items
     * - 500: Internal server error
     */
    Route::post('orders/{orderId}/confirm', [OrderController::class, 'confirm'])
        ->name('orders.confirm');
});
