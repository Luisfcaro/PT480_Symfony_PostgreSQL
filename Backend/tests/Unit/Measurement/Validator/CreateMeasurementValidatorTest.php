<?php

namespace App\Tests\Unit\Measurement\Validator;

use PHPUnit\Framework\TestCase;
use App\Validator\Measurement\CreateMeasurementValidator;
use Symfony\Component\Validator\Validation;
use App\DTO\Measurement\CreateMeasurementDTO;

class CreateMeasurementValidatorTest extends TestCase
{
    private $validator;
    private $createMeasurementValidator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
        $this->createMeasurementValidator = new CreateMeasurementValidator($this->validator);
    }

    public function testValidData()
    {
        $dto = new CreateMeasurementDTO();
        $dto->setYear(2024);
        $dto->setSensorId(1);
        $dto->setWineId(2);
        $dto->setColor('red');
        $dto->setTemperature(12.5);
        $dto->setGraduation(13.5);
        $dto->setPh(3.6);

        $this->createMeasurementValidator->validateCreateMeasurementData($dto);

        $this->assertTrue(true);
    }

    public function testYearIsBlank()
    {
        $this->expectException(\TypeError::class);
        // $this->expectExceptionMessage("Validation failed: Field '[year]': This value should not be blank.");

        $dto = new CreateMeasurementDTO();
        $dto->setYear(null);
        $dto->setSensorId(1);
        $dto->setWineId(2);
        $dto->setColor('red');
        $dto->setTemperature(12.5);
        $dto->setGraduation(13.5);
        $dto->setPh(3.6);

        $this->createMeasurementValidator->validateCreateMeasurementData($dto);
    }

    public function testYearIsNotAnInt()
    {
        $this->expectException(\TypeError::class);
        // $this->expectExceptionMessage("Validation failed: Field '[year]': Year has to be an int value");

        $dto = new CreateMeasurementDTO();
        $dto->setYear('invalid');
        $dto->setSensorId(1);
        $dto->setWineId(2);
        $dto->setColor('red');
        $dto->setTemperature(12.5);
        $dto->setGraduation(13.5);
        $dto->setPh(3.6);

        $this->createMeasurementValidator->validateCreateMeasurementData($dto);
    }

    public function testSensorIdIsBlank()
    {
        $this->expectException(\TypeError::class);
        // $this->expectExceptionMessage("Validation failed: Field '[sensorId]': This value should not be blank.");

        $dto = new CreateMeasurementDTO();
        $dto->setYear(2024);
        $dto->setSensorId(null);
        $dto->setWineId(2);
        $dto->setColor('red');
        $dto->setTemperature(12.5);
        $dto->setGraduation(13.5);
        $dto->setPh(3.6);

        $this->createMeasurementValidator->validateCreateMeasurementData($dto);
    }

    public function testSensorIdIsNotAnInt()
    {
        $this->expectException(\TypeError::class);
        // $this->expectExceptionMessage("Validation failed: Field '[sensorId]': SensorId has to be an int value");

        $dto = new CreateMeasurementDTO();
        $dto->setYear(2024);
        $dto->setSensorId('Invalid');
        $dto->setWineId(2);
        $dto->setColor('red');
        $dto->setTemperature(12.5);
        $dto->setGraduation(13.5);
        $dto->setPh(3.6);

        $this->createMeasurementValidator->validateCreateMeasurementData($dto);
    }

    public function testWineIdIsNotAnInt()
    {
        $this->expectException(\TypeError::class);
        // $this->expectExceptionMessage("Validation failed: Field '[wineId]': SensorId has to be an int value");

        $dto = new CreateMeasurementDTO();
        $dto->setYear(2024);
        $dto->setSensorId(1);
        $dto->setWineId('Invalid');
        $dto->setColor('red');
        $dto->setTemperature(12.5);
        $dto->setGraduation(13.5);
        $dto->setPh(3.6);

        $this->createMeasurementValidator->validateCreateMeasurementData($dto);
    }

    // Additional tests for wineId, color, temperature, graduation, and ph...
    public function testColorIsBlank()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[color]': This value should not be blank.");

        $dto = new CreateMeasurementDTO();
        $dto->setYear(2024);
        $dto->setSensorId(1);
        $dto->setWineId(2);
        $dto->setColor('');
        $dto->setTemperature(12.5);
        $dto->setGraduation(13.5);
        $dto->setPh(3.6);

        $this->createMeasurementValidator->validateCreateMeasurementData($dto);
    }

    public function testTemperatureIsNotAFloat()
    {
        $this->expectException(\TypeError::class);
        // $this->expectExceptionMessage("Validation failed: Field '[temperature]': Temperature has to be an float value");

        $dto = new CreateMeasurementDTO();
        $dto->setYear(2024);
        $dto->setSensorId(1);
        $dto->setWineId(2);
        $dto->setColor('red');
        $dto->setTemperature('Invalid');
        $dto->setGraduation(13.5);
        $dto->setPh(3.6);

        $this->createMeasurementValidator->validateCreateMeasurementData($dto);
    }

    public function testPhIsNotAFloat()
    {
        $this->expectException(\TypeError::class);
        // $this->expectExceptionMessage("Validation failed: Field '[ph]': Ph has to be an float value");

        $dto = new CreateMeasurementDTO();
        $dto->setYear(2024);
        $dto->setSensorId(1);
        $dto->setWineId(2);
        $dto->setColor('red');
        $dto->setTemperature(12.5);
        $dto->setGraduation(13.5);
        $dto->setPh('Invalid');

        $this->createMeasurementValidator->validateCreateMeasurementData($dto);
    }
}

