<?php

namespace App\Orders\Application\UseCase\CreateOrder;

use App\Orders\Domain\Entity\Order;
use App\Orders\Domain\Repository\OrderRepositoryInterface;
use App\Shared\Application\Service\IdGeneratorInterface;

/**
 * WHY IT EXISTS:
 * Central orchestrator for the CreateOrder use case. Handles the business flow:
 * generates a unique order ID, creates an Order via Domain factory method,
 * and persists it. Coordinates Domain objects and Repository interfaces.
 *
 * WHAT IT DOES:
 * 1. Generates a unique order ID via IdGenerator
 * 2. Uses Domain factory method to create Order with proper initial state (DRAFT status, no items yet, event recorded)
 * 3. Persists Order via Repository
 * 4. Returns CreateOrderResult with the created order ID
 */
final class CreateOrderHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly IdGeneratorInterface $idGenerator,
    ) {
    }

    /**
     * Execute the CreateOrder use case.
     */
    public function handle(CreateOrderCommand $command): CreateOrderResult
    {
        // Generate unique order ID
        $orderId = $this->idGenerator->generate();

        // Use Domain factory to create Order with proper initial state
        // Order starts in DRAFT status, has no items, and records OrderCreated event
        $order = Order::create($orderId, $command->userId());

        // Persist Order via Repository
        $this->orderRepository->save($order);

        // Return result with created order ID
        return new CreateOrderResult($orderId);
    }
}
