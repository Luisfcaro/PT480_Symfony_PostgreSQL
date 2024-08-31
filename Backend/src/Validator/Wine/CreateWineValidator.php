<?php

namespace App\Validator\Wine;

use App\DTO\Wine\CreateWineDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateWineValidator
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateCreateWineData(CreateWineDTO $createWineDTO)
    {
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank()],
            'year' => [
                new Assert\NotBlank(),
                new Assert\Type([
                    'type' => 'int',
                    'message' => 'Year has to be an {{ type }} value.'
                ])
            ],
        ]);

        $violations = $this->validator->validate([
            'name' => $createWineDTO->getName(),
            'year' => $createWineDTO->getYear()
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