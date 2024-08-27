<?php

namespace App\Service\User;

use App\DTO\User\RegisterUserDTO;
use App\DTO\User\LoginUserDTO;

interface UserServiceInterface 
{
    public function registerUser(RegisterUserDTO $registerUserDTO);
    public function logUser(LoginUserDTO $loginUserDTO);
}