<?php

namespace App\Validator\Sensor;

use App\DTO\Sensor\GetAllSensorByNameDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GetAllSensorByNameValidator
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateAllSensorByNameData(GetAllSensorByNameDTO $getAllSensorByNameDTO)
    {
        $constraints = new Assert\Collection([
            'order' => [
                new Assert\NotBlank([
                    'message' => 'Order needs a value'
                ]),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'Order needs to be a {{ type }} value'
                ]),
                new Assert\Range([
                    'min' => 0,
                    'max' => 1,
                    'notInRangeMessage' => 'Order has to be between {{ min }} and {{ max }}'
                ])
            ],
        ]);

        $violations = $this->validator->validate([
            'order' => $getAllSensorByNameDTO->getOrder(),
        ], $constraints);

        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath(); 
                $message = $violation->getMessage();
                $errorMessages[] = sprintf("Field '%s': %s", $propertyPath, $message);
            }
    
            throw new \Exception("Validation failed: " . implode(", ", $errorMessages), 401);
        }
    }
}