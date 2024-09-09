<?php

namespace App\Tests\Integration;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\DTO\User\LoginUserDTO;
use App\DTO\User\RegisterUserDTO;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use App\DataFixtures\UserFixtures;
use Exception;

class UserServiceIntegrationTest extends KernelTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    /** @var UserRepository */
    protected $userRepository;

    /** @var UserService */
    protected $userService;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->userService = static::getContainer()->get(UserService::class);
    }

    public function testUserRegisteredSuccesfully(): void
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class
        ]);

        $registerDTO = new RegisterUserDTO();
        $registerDTO->setName('Luis');
        $registerDTO->setSurname('Fernández');
        $registerDTO->setEmail('luis@luis.com');
        $registerDTO->setPassword('patata');

        $registeredUser = $this->userService->registerUser($registerDTO);
        $this->assertNull($registeredUser);

        $userInDatabase = $this->userRepository->findOneBy(['name' => 'Luis']);
        $this->assertNotNull($userInDatabase);
    }

    public function testUserRegisteredWithThatEmail(): void
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class
        ]);

        $registerDTO = new RegisterUserDTO();
        $registerDTO->setName('Luis');
        $registerDTO->setSurname('Fernández');
        $registerDTO->setEmail('email@email.com');
        $registerDTO->setPassword('patata');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User with this mail already exists');

        $this->userService->registerUser($registerDTO);
    }

    public function testUserLoginSuccesfull(): void
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class
        ]);

        $loginDTO = new LoginUserDTO();
        $loginDTO->setEmail('email@email.com');
        $loginDTO->setPassword('password1');

        $bearer = $this->userService->logUser($loginDTO);

        $this->assertNotNull($bearer);
    }

    public function testUserLoginFailed(): void
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class
        ]);

        $loginDTO = new LoginUserDTO();
        $loginDTO->setEmail('email@email.com');
        $loginDTO->setPassword('password7');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid password');

        $this->userService->logUser($loginDTO);
    }
}
