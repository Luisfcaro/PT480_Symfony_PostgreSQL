<?php

namespace App\Tests\Unit\Sensor\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Sensor\CreateSensorValidator;
use App\DTO\Sensor\CreateSensorDTO;

class CreateSensorValidatorTest extends TestCase
{
    private $validator;
    private $createSensorValidator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
        $this->createSensorValidator = new CreateSensorValidator($this->validator);
    }

    public function testValidateCreateSensorDataWithValidData()
    {
        $dto = new CreateSensorDTO();
        $dto->setName('Sensor 1');

        $result = $this->createSensorValidator->validateCreateSensorData($dto);

        $this->assertTrue(true);
    }

    public function testValidateCreateSensorDataWithInvalidData()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[name]': This value should not be blank.");

        $dto = new CreateSensorDTO();
        $dto->setName('');

        $this->createSensorValidator->validateCreateSensorData($dto);
    }
}
