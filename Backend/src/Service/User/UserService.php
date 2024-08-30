<?php

namespace App\Service\User;

use App\DTO\User\LoginUserDTO;
use App\DTO\User\RegisterUserDTO;
use App\Repository\UserRepository;
use App\Validator\User\RegisterValidator;
use App\Validator\User\LogValidator;
use App\Mapper\User\UserMapper;
use App\Service\User\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class UserService implements UserServiceInterface 
{
    private $jwtEncoder;
    private $userRepository;
    private $registerValidator;
    private $logValidator;
    private $userMapper;
    private $passwordHasher;
    private $entityManager;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        JWTEncoderInterface $jwtEncoder,
        UserRepository $userRepository,
        RegisterValidator $registerValidator,
        LogValidator $logValidator,
        UserMapper $userMapper,
        EntityManagerInterface $entityManager,
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
        $this->registerValidator = $registerValidator;
        $this->logValidator = $logValidator;
        $this->userMapper = $userMapper;
        $this->entityManager = $entityManager;
    }

    public function registerUser(RegisterUserDTO $registerUserDTO)
    {
        $this->registerValidator->validateRegisterData($registerUserDTO);

        $userExist = $this->userRepository->findOneBy(['email' => $registerUserDTO->getEmail()]);

        if ($userExist){
            throw new \Exception('User with this mail already exists', 409);
        }

        $user = $this->userMapper->registerDtoToEntity($registerUserDTO);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $registerUserDTO->getPassword());
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);

        $this->entityManager->flush();
    }

    public function logUser(LoginUserDTO $loginUserDTO)
    {
        $this->logValidator->validateLoginData($loginUserDTO);

        $user = $this->userRepository->findOneBy(['email' => $loginUserDTO->getEmail()]);

        if (!$user) {
            throw new \Exception('User not found', 401);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $loginUserDTO->getPassword())) {
            throw new \Exception('Invalid password', 402);
        }

        $bearer = $this->jwtEncoder->encode([
            'email' => $user->getEmail(),
            'exp' => time() + (60 * 60),
        ]);

        return $bearer;
    }
}