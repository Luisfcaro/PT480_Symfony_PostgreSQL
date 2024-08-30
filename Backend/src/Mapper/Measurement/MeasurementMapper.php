<?php

namespace App\Mapper\Measurement;

use App\Entity\Measurement;
use App\Entity\Sensor;
use App\Entity\Wine;
use App\DTO\Measurement\CreateMeasurementDTO;
use App\DTO\Measurement\MeasurementDTO;
use App\Mapper\Sensor\SensorMapper;
use App\Mapper\Wine\WineMapper;

class MeasurementMapper
{
    private $sensorMapper;
    private $wineMapper;

    public function __construct(
        SensorMapper $sensorMapper,
        WineMapper $wineMapper
    )
    {
        $this->sensorMapper = $sensorMapper;
        $this->wineMapper = $wineMapper;
    }

    public function createMeasurementProcessToEntity(CreateMeasurementDTO $createMeasurementDTO, Sensor $sensor, Wine $wine): Measurement
    {
        $measurement = new Measurement();
        $measurement->setYear($createMeasurementDTO->getYear());
        $measurement->setSensorId($sensor);
        $measurement->setWineId($wine);
        $measurement->setColor($createMeasurementDTO->getColor());
        $measurement->setTemperature($createMeasurementDTO->getTemperature());
        $measurement->setGraduation($createMeasurementDTO->getGraduation());
        $measurement->setPh($createMeasurementDTO->getPh());

        return $measurement;
    }

    public function entityToMeasurementDTO(Measurement $measurement): MeasurementDTO
    {
        $measurementDTO = new MeasurementDTO();
        $measurementDTO->setId($measurement->getId());
        $measurementDTO->setYear($measurement->getYear());
        $measurementDTO->setSensor($this->sensorMapper->entityToSensorDTO($measurement->getSensorId()));
        $measurementDTO->setWine($this->wineMapper->entityToWineDTO($measurement->getWineId()));
        $measurementDTO->setColor($measurement->getColor());
        $measurementDTO->setTemperature($measurement->getTemperature());
        $measurementDTO->setGraduation($measurement->getGraduation());
        $measurementDTO->setPh($measurement->getPh());

        return $measurementDTO;
    }
}