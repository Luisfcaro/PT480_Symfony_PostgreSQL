<?php

namespace App\Service\Wine;

use App\DTO\Wine\CreateWineDTO;

interface WineServiceInterface
{
    public function createWine(CreateWineDTO $createWineDTO);
    public function findAllWineWithITSMeasurements();
}