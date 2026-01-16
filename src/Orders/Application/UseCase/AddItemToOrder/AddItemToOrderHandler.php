<?php

namespace App\Orders\Application\UseCase\AddItemToOrder;

use App\Orders\Domain\Exception\InvalidOrderStateException;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\Orders\Domain\ValueObject\OrderItem;

/**
 * WHY IT EXISTS:
 * Central orchestrator for the AddItemToOrder use case. Handles the business flow:
 * retrieves an existing order, creates an OrderItem with Domain validation,
 * adds it to the order (Domain enforces state invariants), and persists the change.
 * Coordinates Domain objects and Repository interfaces.
 *
 * WHAT IT DOES:
 * 1. Retrieves Order from Repository by order ID
 * 2. Throws exception if order not found (Domain rule: cannot add item to non-existent order)
 * 3. Creates OrderItem value object (Domain validates quantity and price > 0)
 * 4. Adds item to order via addItem method (Domain enforces DRAFT status requirement)
 * 5. Persists Order via Repository
 * 6. Returns AddItemToOrderResult confirming the item was added
 */
final class AddItemToOrderHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    /**
     * Execute the AddItemToOrder use case.
     *
     * @throws InvalidOrderStateException If order not found or already confirmed
     * @throws \DomainException If item details are invalid (quantity or price <= 0)
     */
    public function handle(AddItemToOrderCommand $command): AddItemToOrderResult
    {
        // Retrieve Order from Repository
        $order = $this->orderRepository->getById($command->orderId());

        // Domain rule: order must exist
        if ($order === null) {
            throw new InvalidOrderStateException(
                sprintf('Order with ID %d not found', $command->orderId())
            );
        }

        // Create OrderItem value object (Domain validates quantity and unitPrice > 0)
        $item = new OrderItem(
            $command->productId(),
            $command->quantity(),
            $command->unitPrice()
        );

        // Add item to order (Domain enforces DRAFT status requirement)
        // Will throw InvalidOrderStateException if order is already confirmed/cancelled
        $order->addItem($item);

        // Persist Order via Repository with the new item
        $this->orderRepository->save($order);

        // Return result confirming the item was added
        return new AddItemToOrderResult($command->orderId());
    }
}
