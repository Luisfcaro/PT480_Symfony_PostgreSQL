<?php

namespace App\Tests\Unit\Sensor\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use App\DTO\Sensor\GetAllSensorByNameDTO;
use App\Validator\Sensor\GetAllSensorByNameValidator;

class GetAllSensorByNameValidatorTest extends TestCase
{
    private $validator;
    private $getAllSensorByNameValidator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
        $this->getAllSensorByNameValidator = new GetAllSensorByNameValidator($this->validator);
    }

    public function testValidOrder()
    {
        $dto = new GetAllSensorByNameDTO();
        $dto->setOrder(1);

        $this->getAllSensorByNameValidator->validateAllSensorByNameData($dto);

        $this->assertTrue(true);
    }

    public function testOrderIsBlank()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[order]': Order needs a value");

        $dto = new GetAllSensorByNameDTO();
        $dto->setOrder('');

        $this->getAllSensorByNameValidator->validateAllSensorByNameData($dto);
    }

    public function testOrderIsNotNumeric()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[order]': Order needs to be a numeric value");

        $dto = new GetAllSensorByNameDTO();
        $dto->setOrder('notANumber');

        $this->getAllSensorByNameValidator->validateAllSensorByNameData($dto);
    }

    public function testOrderOutOfRange()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[order]': Order has to be between 0 and 1");

        $dto = new GetAllSensorByNameDTO();
        $dto->setOrder(2);

        $this->getAllSensorByNameValidator->validateAllSensorByNameData($dto);
    }
}

