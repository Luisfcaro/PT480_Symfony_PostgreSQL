<?php

namespace App\Tests\Unit\User\Validator;

use PHPUnit\Framework\TestCase;
use App\Validator\User\LogValidator;
use Symfony\Component\Validator\Validation;
use App\DTO\User\LoginUserDTO;

class LogValidatorTest extends TestCase
{
    private $validator;
    private $logValidator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
        $this->logValidator = new LogValidator($this->validator);
    }

    public function testValidData()
    {
        $dto = new LoginUserDTO();
        $dto->setEmail('user@example.com');
        $dto->setPassword('password123');

        $this->logValidator->validateLoginData($dto);

        $this->assertTrue(true);
    }

    public function testEmailIsBlank()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[email]': This value should not be blank.");

        $dto = new LoginUserDTO();
        $dto->setEmail('');
        $dto->setPassword('password123');

        $this->logValidator->validateLoginData($dto);
    }

    public function testEmailIsNotValid()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[email]': This value is not a valid email address.");

        $dto = new LoginUserDTO();
        $dto->setEmail('invalid-email');
        $dto->setPassword('password123');

        $this->logValidator->validateLoginData($dto);
    }

    public function testPasswordIsBlank()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[password]': This value should not be blank.");

        $dto = new LoginUserDTO();
        $dto->setEmail('user@example.com');
        $dto->setPassword('');

        $this->logValidator->validateLoginData($dto);
    }
}

