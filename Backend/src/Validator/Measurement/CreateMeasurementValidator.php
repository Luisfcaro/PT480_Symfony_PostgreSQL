<?php

namespace App\Validator\Measurement;

use App\DTO\Measurement\CreateMeasurementDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateMeasurementValidator
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
    
    public function validateCreateMeasurementData(CreateMeasurementDTO $createMeasurementDTO)
    {
        $constraints = new Assert\Collection([
            'year' => [
                new Assert\NotBlank(),
                new Assert\Type([
                    'type' => 'int',
                    'message' => 'Year has to be an {{ type }} value'
                ])
            ],
            'sensorId' => [
                new Assert\NotBlank(),
                new Assert\Type([
                    'type' => 'int',
                    'message' => 'SensorId has to be an {{ type }} value'
                ])
            ],
            'wineId' => [
                new Assert\NotBlank(),
                new Assert\Type([
                    'type' => 'int',
                    'message' => 'WineId has to be an {{ type }} value'
                ])
            ],
            'color' => [
                new Assert\NotBlank(),
                new Assert\Type([
                    'type' => 'string',
                    'message' => 'Color has to be an {{ type }} value'
                ])
            ],
            'temperature' => [
                new Assert\NotBlank(),
                new Assert\Type([
                    'type' => 'float',
                    'message' => 'Temperature has to be an {{ type }} value'
                ])
            ],
            'graduation' => [
                new Assert\NotBlank(),
                new Assert\Type([
                    'type' => 'float',
                    'message' => 'Graduation has to be an {{ type }} value'
                ])
            ],
            'ph' => [
                new Assert\NotBlank(),
                new Assert\Type([
                    'type' => 'float',
                    'message' => 'Ph has to be an {{ type }} value'
                ])
            ],
        ]);

        $violations = $this->validator->validate([
            'year' => $createMeasurementDTO->getYear(),
            'sensorId' => $createMeasurementDTO->getSensorId(),
            'wineId' => $createMeasurementDTO->getWineId(),
            'color' => $createMeasurementDTO->getColor(),
            'temperature' => $createMeasurementDTO->getTemperature(),
            'graduation' => $createMeasurementDTO->getGraduation(),
            'ph' => $createMeasurementDTO->getPh()
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