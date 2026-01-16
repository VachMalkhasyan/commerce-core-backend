<?php

namespace App\Shared\Application\Service;

/**
 * WHY IT EXISTS:
 * Abstracts ID generation away from Application layer to keep it testable.
 * Implementations can use UUIDs, auto-incrementing IDs, or any other strategy
 * without affecting the use case logic.
 *
 * WHAT IT DOES:
 * Defines a contract for generating unique identifiers across the application.
 */
interface IdGeneratorInterface
{
    /**
     * Generate a unique identifier.
     */
    public function generate(): string;
}
