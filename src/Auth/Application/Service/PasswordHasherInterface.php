<?php

namespace App\Auth\Application\Service;

/**
 * WHY IT EXISTS:
 * Abstracts password hashing logic from the Application layer, allowing different
 * hashing algorithms or strategies without changing the RegisterUser handler.
 * This keeps the use case testable and decoupled from concrete implementations.
 *
 * WHAT IT DOES:
 * Defines a contract for hashing raw passwords and verifying them against hashes.
 */
interface PasswordHasherInterface
{
    /**
     * Hash a raw password string.
     *
     * @param string $plainPassword The raw password to hash
     * @return string The hashed password
     */
    public function hash(string $plainPassword): string;

    /**
     * Verify a plain password against a hash.
     *
     * @param string $plainPassword The raw password to verify
     * @param string $hash The hash to verify against
     * @return bool True if the password matches, false otherwise
     */
    public function verify(string $plainPassword, string $hash): bool;
}
