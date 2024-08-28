<?php

namespace App\Mapper\Sensor;

use App\Entity\Sensor;
use App\DTO\Sensor\CreateSensorDTO;
use App\DTO\Sensor\SensorDTO;

class SensorMapper
{
    public function createSensorDtoToEntity(CreateSensorDTO $createSensorDTO): Sensor 
    {
        $sensor = new Sensor();
        $sensor->setName($createSensorDTO->getName());

        return $sensor;
    }

    public function entityTocreateSensorDto(Sensor $sensor): CreateSensorDTO
    {
        $createSensorDTO = new CreateSensorDTO();
        $createSensorDTO->setId($sensor->getId());
        $createSensorDTO->setName($sensor->getName());

        return $createSensorDTO;
    }

    public function entityToSensorDTO(Sensor $sensor): SensorDTO
    {
        $sensorDTO = new SensorDTO();
        $sensorDTO->setId($sensor->getId());
        $sensorDTO->setName($sensor->getName());

        return $sensorDTO;
    }
}