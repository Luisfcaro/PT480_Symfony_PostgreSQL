<?php

namespace App\Service\Sensor;

use App\DTO\Sensor\CreateSensorDTO;
use App\DTO\Sensor\GetAllSensorByNameDTO;

interface SensorServiceInterface
{
    public function createSensor(CreateSensorDTO $createSensorDTO);
    public function getAllSensorByName(GetAllSensorByNameDTO $getAllSensorByNameDTO);
}