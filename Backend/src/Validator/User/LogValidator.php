<?php

namespace App\Validator\User;

use App\DTO\User\LoginUserDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LogValidator {
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateLoginData(LoginUserDTO $loginUserDTO)
    {
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\Email(),
                new Assert\NotBlank(),
                new Assert\NotNull(),
            ],
            'password' => [
                new Assert\NotBlank(),
                new Assert\NotNull(),
            ],
        ]);

        $violations = $this->validator->validate([
            'email' => $loginUserDTO->getEmail(),
            'password' => $loginUserDTO->getPassword(),
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