<?php

namespace App\Validator\Sensor;

use App\DTO\Sensor\CreateSensorDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSensorValidator
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateCreateSensorData(CreateSensorDTO $createSensorDTO)
    {
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank()],
        ]);

        $violations = $this->validator->validate([
            'name' => $createSensorDTO->getName(),
        ], $constraints);

        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath(); 
                $message = $violation->getMessage();
                $errorMessages[] = sprintf("Field '%s': %s", $propertyPath, $message);
            }
    
            throw new \Exception("Validation failed: " . implode(", ", $errorMessages), 400);
        }
    }
}