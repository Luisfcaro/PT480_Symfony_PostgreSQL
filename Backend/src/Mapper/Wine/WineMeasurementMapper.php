<?php

namespace App\Mapper\Wine;

use App\Mapper\Measurement\MeasurementMapper;
use App\DTO\Wine\WineMeasurementDTO;
use App\Entity\Wine;


class WineMeasurementMapper
{
    private $measurementMapper;

    public function __construct(
        MeasurementMapper $measurementMapper,
    ) {
        $this->measurementMapper = $measurementMapper;
    }

    public function entityToWineWithMeasurementsDTO(Wine $wine) : WineMeasurementDTO
    {
        $wineDTO = new WineMeasurementDTO();
        $wineDTO->setId($wine->getId());
        $wineDTO->setName($wine->getName());
        $wineDTO->setYear($wine->getYear());

        $wineMeasurements = $wine->getMeasurements();

        foreach ($wineMeasurements as $measurement) {
            $measurementDTO = $this->measurementMapper->entityToMeasurementDTO($measurement);
            $wineDTO->addMeasurement($measurementDTO);
        }

        return $wineDTO;
    }
}