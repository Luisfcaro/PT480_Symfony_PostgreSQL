<?php

namespace App\Service\Measurement;

use App\DTO\Measurement\CreateMeasurementDTO;

interface MeasurementServiceInterface
{
    public function createMeasurement(CreateMeasurementDTO $createMeasurementDTO);
}