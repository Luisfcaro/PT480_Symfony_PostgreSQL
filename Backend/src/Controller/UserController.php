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

    #[Route('/register', name: 'register', methods: ['POST'])]
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
                throw new \Exception('Missing fields: ' . implode(', ', $missingFields));
            }

            $user_exist = $this->UserRepository->findOneBy(['email' => $data['email']]);

            if ($user_exist){
                throw new \Exception('User with this mail already exists');
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
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }


    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {

        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['email', 'password'];

            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                throw new \Exception('Missing fields: ' . implode(', ', $missingFields));
            }

            $user = $this->UserRepository->findOneBy(['email' => $data['email']]);

            if (!$user) {
                throw new \Exception('User not found');
            }

            if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
                throw new \Exception('Invalid password');
            }

            $bearer = $this->jwtEncoder->encode([
                'email' => $user->getEmail(),
                'exp' => time() + (60 * 60), // 1 hour expiration
            ]);

            

            return new JsonResponse(['token' => $bearer], Response::HTTP_OK);

        }catch(\Exception $e){
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

    }
}
