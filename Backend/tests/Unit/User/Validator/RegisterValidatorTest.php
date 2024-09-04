<?php

namespace App\Tests\Unit\User\Validator;

use PHPUnit\Framework\TestCase;
use App\Validator\User\RegisterValidator;
use Symfony\Component\Validator\Validation;
use App\DTO\User\RegisterUserDTO;

class RegisterValidatorTest extends TestCase
{
    private $validator;
    private $registerValidator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
        $this->registerValidator = new RegisterValidator($this->validator);
    }

    public function testValidData()
    {
        $dto = new RegisterUserDTO();
        $dto->setEmail('user@example.com');
        $dto->setPassword('password123');
        $dto->setName('John');
        $dto->setSurname('Doe');

        $this->registerValidator->validateRegisterData($dto);

        $this->assertTrue(true);
    }

    public function testEmailIsBlank()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[email]': This value should not be blank.");

        $dto = new RegisterUserDTO();
        $dto->setEmail('');
        $dto->setPassword('password123');
        $dto->setName('John');
        $dto->setSurname('Doe');

        $this->registerValidator->validateRegisterData($dto);
    }

    public function testEmailIsNotValid()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[email]': This value is not a valid email address.");

        $dto = new RegisterUserDTO();
        $dto->setEmail('invalid-email');
        $dto->setPassword('password123');
        $dto->setName('John');
        $dto->setSurname('Doe');

        $this->registerValidator->validateRegisterData($dto);
    }

    public function testPasswordIsBlank()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[password]': This value should not be blank.");

        $dto = new RegisterUserDTO();
        $dto->setEmail('user@example.com');
        $dto->setPassword('');
        $dto->setName('John');
        $dto->setSurname('Doe');

        $this->registerValidator->validateRegisterData($dto);
    }

    public function testNameIsBlank()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[name]': This value should not be blank.");

        $dto = new RegisterUserDTO();
        $dto->setEmail('user@example.com');
        $dto->setPassword('password123');
        $dto->setName('');
        $dto->setSurname('Doe');

        $this->registerValidator->validateRegisterData($dto);
    }

    public function testSurnameIsBlank()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[surname]': This value should not be blank.");

        $dto = new RegisterUserDTO();
        $dto->setEmail('user@example.com');
        $dto->setPassword('password123');
        $dto->setName('John');
        $dto->setSurname('');

        $this->registerValidator->validateRegisterData($dto);
    }
}

