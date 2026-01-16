<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\Log;

use App\Auth\Application\UseCase\RegisterUser\RegisterUserCommand;
use App\Auth\Application\UseCase\RegisterUser\RegisterUserHandler;
use App\Auth\Application\UseCase\AuthenticateUser\AuthenticateUserCommand;
use App\Auth\Application\UseCase\AuthenticateUser\AuthenticateUserHandler;

use App\Auth\Domain\Exception\UserAlreadyExistsException;
use App\Auth\Domain\Exception\InvalidCredentialsException;

use App\Models\User;

/**
 * Authentication Controller
 *
 * HTTP boundary layer for authentication.
 * No business logic. No domain rules. Just glue.
 */
#[OA\Tag(
    name: 'Authentication',
    description: 'User registration and authentication endpoints'
)]
class AuthController
{
    public function __construct(
        private RegisterUserHandler $registerUserHandler,
        private AuthenticateUserHandler $authenticateUserHandler,
    ) {}

    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        try {
            $command = new RegisterUserCommand(
                email: $validated['email'],
                plainPassword: $validated['password'],
            );

            $result = $this->registerUserHandler->handle($command);

            return response()->json([
                'success' => true,
                'data' => [
                    'userId' => $result->userId(),
                    'email' => $validated['email'],
                ],
                'message' => 'User registered successfully',
            ], 201);

        } catch (UserAlreadyExistsException) {
            return response()->json([
                'success' => false,
                'error' => 'Email already exists',
                'code' => 'USER_ALREADY_EXISTS',
            ], 409);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'code' => 'INVALID_INPUT',
            ], 422);

        } catch (\Throwable $e) {
            Log::error('Registration error', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'error' => 'Registration failed',
                'code' => 'INTERNAL_ERROR',
            ], 500);
        }
    }

    /**
     * Authenticate user and return JWT token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $command = new AuthenticateUserCommand(
                email: $validated['email'],
                plainPassword: $validated['password'],
            );

            // 1. Application layer authentication
            $result = $this->authenticateUserHandler->handle($command);

            // 2. Map Domain userId → Eloquent user
            $user = \App\Models\User::find($result->userId());

            if (!$user) {
                throw new InvalidCredentialsException();
            }

            // 3. Generate JWT token
            $token = auth()->guard('api')->login($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'userId' => $user->id,
                    'token' => $token,
                    'expiresIn' => 3600, // keep simple for now
                ],
                'message' => 'Authentication successful',
            ], 200);

        } catch (InvalidCredentialsException) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid email or password',
                'code' => 'INVALID_CREDENTIALS',
            ], 401);

        } catch (\Throwable $e) {
            \Log::error('Authentication error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Authentication failed',
                'code' => 'INTERNAL_ERROR',
            ], 500);
        }
    }

}
