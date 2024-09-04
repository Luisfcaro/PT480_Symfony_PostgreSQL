<?php

namespace App\Tests\Unit\Wine\Validator;

use PHPUnit\Framework\TestCase;
use App\Validator\Wine\CreateWineValidator;
use Symfony\Component\Validator\Validation;
use App\DTO\Wine\CreateWineDTO;

class CreateWineValidatorTest extends TestCase
{
    private $validator;
    private $createWineValidator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidator();
        $this->createWineValidator = new CreateWineValidator($this->validator);
    }

    public function testValidData()
    {
        $dto = new CreateWineDTO();
        $dto->setName('Chardonnay');
        $dto->setYear(2020);

        $this->createWineValidator->validateCreateWineData($dto);

        $this->assertTrue(true);
    }

    public function testNameIsBlank()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed: Field '[name]': This value should not be blank.");

        $dto = new CreateWineDTO();
        $dto->setName('');
        $dto->setYear(2020);

        $this->createWineValidator->validateCreateWineData($dto);
    }

    // public function testYearIsBlank()
    // {
    //     $this->expectException(\Exception::class);
    //     $this->expectExceptionMessage("Validation failed: Field '[year]': This value should not be blank.");

    //     $dto = new CreateWineDTO();
    //     $dto->setName('Chardonnay');
    //     $dto->setYear(null);

    //     $this->createWineValidator->validateCreateWineData($dto);
    // }

    public function testYearIsNotInteger()
    {
        $this->expectException(\TypeError::class);
        // $this->expectExceptionMessage("Validation failed: Field '[year]': Year has to be an int value.");

        $dto = new CreateWineDTO();
        $dto->setName('Chardonnay');
        $dto->setYear('two thousand and twenty');

        $this->createWineValidator->validateCreateWineData($dto);
    }
}

