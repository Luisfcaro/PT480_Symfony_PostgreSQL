<?php

namespace App\Mapper\User;

use App\DTO\User\RegisterUserDTO;
use App\DTO\User\LoginUserDTO;
use App\Entity\User;

class UserMapper
{
    public function registerDtoToEntity(RegisterUserDTO $registerUserDTO): User
    {
        $user = new User();
        $user->setEmail($registerUserDTO->getEmail());
        $user->setPassword($registerUserDTO->getPassword());
        $user->setName($registerUserDTO->getName());
        $user->setSurname($registerUserDTO->getSurname());

        return $user;
    }

    public function loginDtoToEntity(LoginUserDTO $loginUserDTO): User
    {
        $user = new User();
        $user->setEmail($loginUserDTO->getEmail());
        $user->setPassword($loginUserDTO->getPassword());

        return $user;
    }
}