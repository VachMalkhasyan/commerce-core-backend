<?php

namespace App\Http\Controllers\Api;

use App\Orders\Application\UseCase\AddItemToOrder\AddItemToOrderCommand;
use App\Orders\Application\UseCase\AddItemToOrder\AddItemToOrderHandler;
use App\Orders\Application\UseCase\ConfirmOrder\ConfirmOrderCommand;
use App\Orders\Application\UseCase\ConfirmOrder\ConfirmOrderHandler;
use App\Orders\Application\UseCase\CreateOrder\CreateOrderCommand;
use App\Orders\Application\UseCase\CreateOrder\CreateOrderHandler;
use App\Orders\Domain\Exception\EmptyOrderException;
use App\Orders\Domain\Exception\InvalidOrderStateException;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * WHY IT EXISTS:
 * Acts as the HTTP boundary layer for order operations, converting HTTP requests to Application
 * layer Commands, executing handlers, and returning JSON responses.
 *
 * WHAT IT DOES:
 * Handles order creation, item addition, and confirmation. Validates HTTP input, creates Commands,
 * calls handlers, catches Domain exceptions, and returns appropriate HTTP responses.
 */
class OrderController
{
    public function __construct(
        private CreateOrderHandler $createOrderHandler,
        private AddItemToOrderHandler $addItemToOrderHandler,
        private ConfirmOrderHandler $confirmOrderHandler,
        private OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * Create a new order for the authenticated user.
     *
     * @param  Request  $request  HTTP request
     * @return JsonResponse Order creation response
     *
     * Example Success (201):
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
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // Get authenticated user ID from request (set by auth middleware)
            $userId = auth('api')->id() ?? (int) $request->user('api')?->id;

            if (! $userId) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated',
                    'code' => 'UNAUTHENTICATED',
                ], 401);
            }

            // Create Command
            $command = new CreateOrderCommand(userId: $userId);

            // Execute use case via handler
            $result = $this->createOrderHandler->handle($command);

            // Fetch created order to return complete state
            $order = $this->orderRepository->getById($result->orderId());

            // Return success response
            return response()->json([
                'success' => true,
                'data' => [
                    'orderId' => $result->orderId(),
                    'status' => $order->getStatus()->value,
                    'itemCount' => count($order->getItems()),
                    'totalAmount' => $order->totalAmount(),
                ],
                'message' => 'Order created successfully',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create order',
                'code' => 'INTERNAL_ERROR',
            ], 500);
        }
    }

    /**
     * Add an item to an existing order.
     *
     * @param  Request  $request  HTTP request containing item details
     * @param  int  $orderId  Order ID
     * @return JsonResponse Item addition response
     *
     * Example Request Body:
     * {
     *     "productId": 789,
     *     "quantity": 2,
     *     "unitPrice": 2999
     * }
     *
     * Example Success (200):
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
     * Example Error - Order not found (404):
     * {
     *     "success": false,
     *     "error": "Order not found",
     *     "code": "NOT_FOUND"
     * }
     *
     * Example Error - Order already confirmed (409):
     * {
     *     "success": false,
     *     "error": "Cannot add item to confirmed order",
     *     "code": "INVALID_ORDER_STATE"
     * }
     */
    public function addItem(Request $request, int $orderId): JsonResponse
    {
        // Validate HTTP input
        $validated = $request->validate([
            'productId' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unitPrice' => ['required', 'integer', 'min:1'],
        ]);

        try {
            // Create Command
            $command = new AddItemToOrderCommand(
                orderId: $orderId,
                productId: $validated['productId'],
                quantity: $validated['quantity'],
                unitPrice: $validated['unitPrice'],
            );

            // Execute use case via handler
            $result = $this->addItemToOrderHandler->handle($command);

            // Fetch updated order
            $order = $this->orderRepository->getById($result->orderId());

            // Return success response
            return response()->json([
                'success' => true,
                'data' => [
                    'orderId' => $result->orderId(),
                    'itemCount' => count($order->getItems()),
                    'totalAmount' => $order->totalAmount(),
                ],
                'message' => 'Item added to order',
            ], 200);

        } catch (InvalidOrderStateException $e) {
            // Order not found or already confirmed
            if (str_contains($e->getMessage(), 'not found')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Order not found',
                    'code' => 'NOT_FOUND',
                ], 404);
            }

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'code' => 'INVALID_ORDER_STATE',
            ], 409);

        } catch (\DomainException $e) {
            // Invalid quantity or price
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'code' => 'INVALID_INPUT',
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to add item',
                'code' => 'INTERNAL_ERROR',
            ], 500);
        }
    }

    /**
     * Confirm and finalize an order.
     *
     * @param  Request  $request  HTTP request
     * @param  int  $orderId  Order ID to confirm
     * @return JsonResponse Order confirmation response
     *
     * Example Success (200):
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
     * Example Error - Empty order (422):
     * {
     *     "success": false,
     *     "error": "Order must contain at least one item",
     *     "code": "EMPTY_ORDER"
     * }
     *
     * Example Error - Order not found (404):
     * {
     *     "success": false,
     *     "error": "Order not found",
     *     "code": "NOT_FOUND"
     * }
     */
    public function confirm(Request $request, int $orderId): JsonResponse
    {
        try {
            // Create Command
            $command = new ConfirmOrderCommand(orderId: $orderId);

            // Execute use case via handler
            $result = $this->confirmOrderHandler->handle($command);

            // Fetch confirmed order
            $order = $this->orderRepository->getById($result->orderId());

            // Convert cents to dollars for display
            $totalInDollars = $result->totalAmount() / 100;

            // Return success response
            return response()->json([
                'success' => true,
                'data' => [
                    'orderId' => $result->orderId(),
                    'status' => $order->getStatus()->value,
                    'itemCount' => count($order->getItems()),
                    'totalAmount' => $result->totalAmount(),
                    'formattedTotal' => sprintf('$%.2f', $totalInDollars),
                ],
                'message' => 'Order confirmed successfully',
            ], 200);

        } catch (EmptyOrderException $e) {
            // Order has no items
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'code' => 'EMPTY_ORDER',
            ], 422);

        } catch (InvalidOrderStateException $e) {
            // Order not found or already confirmed
            if (str_contains($e->getMessage(), 'not found')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Order not found',
                    'code' => 'NOT_FOUND',
                ], 404);
            }

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'code' => 'INVALID_ORDER_STATE',
            ], 409);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to confirm order',
                'code' => 'INTERNAL_ERROR',
            ], 500);
        }
    }

    /**
     * Get order details by ID.
     *
     * @param  int  $orderId  Order ID
     * @return JsonResponse Order details
     *
     * Example Success (200):
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
     */
    public function show(int $orderId): JsonResponse
    {
        try {
            $order = $this->orderRepository->getById($orderId);

            if (! $order) {
                return response()->json([
                    'success' => false,
                    'error' => 'Order not found',
                    'code' => 'NOT_FOUND',
                ], 404);
            }

            // Format items for response
            $items = array_map(fn ($item) => [
                'productId' => $item->productId(),
                'quantity' => $item->quantity(),
                'unitPrice' => $item->unitPrice(),
                'total' => $item->total(),
            ], $order->getItems());

            return response()->json([
                'success' => true,
                'data' => [
                    'orderId' => $order->getId(),
                    'userId' => $order->getUserId(),
                    'status' => $order->getStatus()->value,
                    'itemCount' => count($order->getItems()),
                    'items' => $items,
                    'totalAmount' => $order->totalAmount(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve order',
                'code' => 'INTERNAL_ERROR',
            ], 500);
        }
    }
}
