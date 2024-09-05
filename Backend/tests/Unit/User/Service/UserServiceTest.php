<?php

namespace App\Tests\Unit\User\Service;

use PHPUnit\Framework\TestCase;
use App\Service\User\UserService;
use App\Repository\UserRepository;
use App\Validator\User\RegisterValidator;
use App\Validator\User\LogValidator;
use App\Mapper\User\UserMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\DTO\User\RegisterUserDTO;
use App\DTO\User\LoginUserDTO;
use App\Entity\User;

class UserServiceTest extends TestCase
{
    private $userService;
    private $userRepositoryMock;
    private $registerValidatorMock;
    private $logValidatorMock;
    private $userMapperMock;
    private $passwordHasherMock;
    private $jwtEncoderMock;
    private $entityManagerMock;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->registerValidatorMock = $this->createMock(RegisterValidator::class);
        $this->logValidatorMock = $this->createMock(LogValidator::class);
        $this->userMapperMock = $this->createMock(UserMapper::class);
        $this->passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtEncoderMock = $this->createMock(JWTEncoderInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->userService = new UserService(
            $this->passwordHasherMock,
            $this->jwtEncoderMock,
            $this->userRepositoryMock,
            $this->registerValidatorMock,
            $this->logValidatorMock,
            $this->userMapperMock,
            $this->entityManagerMock
        );
    }

    public function testRegisterUserSuccess()
    {
        $registerUserDTO = new RegisterUserDTO();
        $registerUserDTO->setEmail('test@test.com');
        $registerUserDTO->setPassword('password');
        $registerUserDTO->setName('Luis');
        $registerUserDTO->setSurname('Fernández');

        $this->registerValidatorMock
            ->expects($this->once())
            ->method('validateRegisterData')
            ->with($this->equalTo($registerUserDTO));

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@test.com'])
            ->willReturn(null);

        $userEntity = new User();
        $userEntity->setEmail('test@test.com');
        $userEntity->setName('Luis');
        $userEntity->setSurname('Fernández');

        $this->userMapperMock
            ->expects($this->once())
            ->method('registerDtoToEntity')
            ->with($this->equalTo($registerUserDTO))
            ->willReturn($userEntity);

        $this->passwordHasherMock
            ->expects($this->once())
            ->method('hashPassword')
            ->with($userEntity, 'password')
            ->willReturn('hashedPassword');

        $userEntity->setPassword('hashedPassword');

        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($userEntity);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->userService->registerUser($registerUserDTO);

        $this->assertEquals('hashedPassword', $userEntity->getPassword());
    }

    public function testRegisterUserAlreadyExists()
    {
        $registerUserDTO = new RegisterUserDTO();
        $registerUserDTO->setEmail('test@test.com');
        $registerUserDTO->setPassword('password');
        $registerUserDTO->setName('Luis');
        $registerUserDTO->setSurname('Fernández');

        $userEntity = new User();
        $userEntity->setEmail('test@test.com');
        $userEntity->setName('Luis');
        $userEntity->setSurname('Fernández');
        $userEntity->setPassword('password');

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@test.com'])
            ->willReturn($userEntity);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User with this mail already exists');

        $this->userService->registerUser($registerUserDTO);
    }

    public function testLogUserSuccess()
    {
        $loginUserDTO = new LoginUserDTO();
        $loginUserDTO->setEmail('test@test.com');
        $loginUserDTO->setPassword('password');

        $this->logValidatorMock
            ->expects($this->once())
            ->method('validateLoginData')
            ->with($this->equalTo($loginUserDTO));

        $userEntity = new User();
        $userEntity->setEmail('test@test.com');
        $userEntity->setName('Luis');
        $userEntity->setSurname('Fernández');
        $userEntity->setPassword('password');

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@test.com'])
            ->willReturn($userEntity);

        $this->passwordHasherMock
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($userEntity, 'password')
            ->willReturn(true);

        $this->jwtEncoderMock
            ->expects($this->once())
            ->method('encode')
            ->with([
                'email' => 'test@test.com',
                'exp' => time() + (60 * 60)
            ])
            ->willReturn('jwt_token');

        $result = $this->userService->logUser($loginUserDTO);
        $this->assertEquals('jwt_token', $result);
    }

    public function testLogUserNotFound()
    {
        $loginUserDTO = new LoginUserDTO();
        $loginUserDTO->setEmail('test@test.com');
        $loginUserDTO->setPassword('password');

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@test.com'])
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');

        $this->userService->logUser($loginUserDTO);
    }

    public function testLogUserInvalidPassword()
    {
        $loginUserDTO = new LoginUserDTO();
        $loginUserDTO->setEmail('test@test.com');
        $loginUserDTO->setPassword('password');

        $userEntity = new User();
        $userEntity->setEmail('test@test.com');
        $userEntity->setName('Luis');
        $userEntity->setSurname('Fernández');
        $userEntity->setPassword('ppppppp');

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@test.com'])
            ->willReturn($userEntity);

        $this->passwordHasherMock
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($userEntity, 'password')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid password');

        $this->userService->logUser($loginUserDTO);
    }
}
