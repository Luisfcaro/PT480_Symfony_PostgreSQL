<?php

namespace App\Validator\User;

use App\DTO\User\RegisterUserDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterValidator 
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateRegisterData(RegisterUserDTO $registerUserDTO)
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
            'name' => [
                new Assert\NotBlank(),
                new Assert\NotNull(),
            ],
            'surname' => [
                new Assert\NotBlank(),
                new Assert\NotNull(),
            ],
        ]);

        $violations = $this->validator->validate([
            'email' => $registerUserDTO->getEmail(),
            'password' => $registerUserDTO->getPassword(),
            'name' => $registerUserDTO->getName(),
            'surname' => $registerUserDTO->getSurname(),
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