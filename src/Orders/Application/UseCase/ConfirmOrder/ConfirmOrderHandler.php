<?php

namespace App\Orders\Application\UseCase\ConfirmOrder;

use App\Orders\Domain\Exception\EmptyOrderException;
use App\Orders\Domain\Exception\InvalidOrderStateException;
use App\Orders\Domain\Repository\OrderRepositoryInterface;

/**
 * WHY IT EXISTS:
 * Central orchestrator for the ConfirmOrder use case. Handles the business flow:
 * retrieves an existing order, confirms it via Domain logic (validates state and items),
 * and persists the state change. Coordinates Domain objects and Repository interfaces.
 *
 * WHAT IT DOES:
 * 1. Retrieves Order from Repository by order ID
 * 2. Throws exception if order not found (Domain rule: cannot confirm non-existent order)
 * 3. Calls order->confirm() which enforces Domain invariants:
 *    - Order must be in DRAFT status
 *    - Order must contain at least one item
 *    - Transitions order to CONFIRMED status
 *    - Records OrderConfirmed event
 * 4. Persists Order via Repository with new confirmed state
 * 5. Returns ConfirmOrderResult with the confirmed order ID and total amount
 */
final class ConfirmOrderHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    /**
     * Execute the ConfirmOrder use case.
     *
     * @throws InvalidOrderStateException If order not found or already confirmed/cancelled
     * @throws EmptyOrderException If order has no items
     */
    public function handle(ConfirmOrderCommand $command): ConfirmOrderResult
    {
        // Retrieve Order from Repository
        $order = $this->orderRepository->getById($command->orderId());

        // Domain rule: order must exist
        if ($order === null) {
            throw new InvalidOrderStateException(
                sprintf('Order with ID %d not found', $command->orderId())
            );
        }

        // Call confirm() on order - Domain enforces all invariants:
        // - DRAFT status only
        // - At least one item required
        // - Transitions to CONFIRMED status
        // - Records OrderConfirmed event
        // Will throw EmptyOrderException or InvalidOrderStateException if rules violated
        $order->confirm();

        // Persist Order via Repository with new state
        $this->orderRepository->save($order);

        // Return result with confirmed order ID and total amount
        return new ConfirmOrderResult(
            $command->orderId(),
            $order->totalAmount()
        );
    }
}
