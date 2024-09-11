<?php

namespace App\Controller;

use App\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use App\DTO\User\RegisterUserDTO;
use App\DTO\User\LoginUserDTO;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(
        UserServiceInterface $userService
    ) {
        $this->userService = $userService;
    }

    #[Route('api/register', name: 'register', methods: ['POST'])]
    #[OA\Post(
        path: "/api/register",
        summary: "Register a new user",
        tags: ["User Management"],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John"),
                    new OA\Property(property: "surname", type: "string", example: "Doe"),
                    new OA\Property(property: "email", type: "string", example: "john.doe@example.com"),
                    new OA\Property(property: "password", type: "string", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User registered successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User registered successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Missing fields on request body or validation failed",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Validation failed: Field '[name]': This value should not be blank., ...")
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: "User with email already exist",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "User with this email already exists")
                    ]
                )
            )
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $registerUserDTO = new RegisterUserDTO();
            $registerUserDTO->setName($data['name'] ?? null);
            $registerUserDTO->setSurname($data['surname'] ?? null);
            $registerUserDTO->setEmail($data['email'] ?? null);
            $registerUserDTO->setPassword($data['password'] ?? null);

            $this->userService->registerUser($registerUserDTO);

            return new JsonResponse([
                'message' => 'User registered successfully',
            ], Response::HTTP_CREATED);

        } catch(\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('api/login', name: 'login', methods: ['POST'])]
    #[OA\Post(
        path: "/api/login",
        summary: "Authenticate a user",
        tags: ["User Management"],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "email", type: "string", example: "john.doe@example.com"),
                    new OA\Property(property: "password", type: "string", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful authentication",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "User logged"
                        ),
                        new OA\Property(property: "token", type: "string", example: "your-jwt-token")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "No user with that email",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "User not found")
                    ]
                )
            ),
            new OA\Response(
                response: 402,
                description: "Incorrect Password",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Invalid Password")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Missing fields or validation failed",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Validation failed: Field '[email]': This value is not a valid email address.")
                    ]
                )
            )
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $loginUserDTO = new LoginUserDTO();
            $loginUserDTO->setEmail($data['email'] ?? null);
            $loginUserDTO->setPassword($data['password'] ?? null);

            $bearer = $this->userService->logUser($loginUserDTO);

            return new JsonResponse([
                'message' => 'User logged',
                'token' => $bearer
            ], Response::HTTP_OK);

        } catch(\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
