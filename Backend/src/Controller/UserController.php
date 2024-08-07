<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

use OpenApi\Attributes as OA;

class UserController extends AbstractController
{

    private $jwtEncoder;
    private $UserRepository;
    private $passwordHasher;
    private $entityManager;

    public function __construct(

        UserPasswordHasherInterface $passwordHasher,
        JWTEncoderInterface $jwtEncoder,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,

    ){

        $this->passwordHasher = $passwordHasher;
        $this->jwtEncoder = $jwtEncoder;
        $this->UserRepository = $userRepository;
        $this->entityManager = $entityManager;

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
                description: "Missing fields on request body",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Missing fields: name, surname, ...")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
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

            $requiredFields = ['name', 'surname', 'email', 'password'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                throw new \Exception('Missing fields: ' . implode(', ', $missingFields), 400);
            }

            $user_exist = $this->UserRepository->findOneBy(['email' => $data['email']]);

            if ($user_exist){
                throw new \Exception('User with this mail already exists', 401);
            }

            $user = new User();
            $user->setName($data['name']);
            $user->setSurname($data['surname']);
            $user->setEmail($data['email']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            

            $this->entityManager->persist($user);

            $this->entityManager->flush();

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
                response: 403,
                description: "Missing fields on request body",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Missing Fields: ...")
                    ]
                )
            )
        ]
    )]
    public function login(Request $request): JsonResponse
    {

        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['email', 'password'];

            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || $data[$field] == "" || $data[$field] === null) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                throw new \Exception('Missing fields: ' . implode(', ', $missingFields), 403);
            }

            $user = $this->UserRepository->findOneBy(['email' => $data['email']]);

            if (!$user) {
                throw new \Exception('User not found', 401);
            }

            if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
                throw new \Exception('Invalid password', 402);
            }

            $bearer = $this->jwtEncoder->encode([
                'email' => $user->getEmail(),
                'exp' => time() + (60 * 60), // 1 hour expiration
            ]);

            

            return new JsonResponse(['token' => $bearer], Response::HTTP_OK);

        }catch(\Exception $e){
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }

    }
}
