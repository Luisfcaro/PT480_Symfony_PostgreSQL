<?php

namespace App\Mapper\Wine;

use App\Entity\Wine;
use App\DTO\Wine\WineDTO;
use App\DTO\Wine\CreateWineDTO;
use App\Mapper\Measurement\MeasurementMapper;

class WineMapper
{
    public function createWineDTOToEntity(CreateWineDTO $createWineDTO): Wine
    {
        $wine = new Wine();
        $wine->setName($createWineDTO->getName());
        $wine->setYear($createWineDTO->getYear());

        return $wine;
    }

    public function entityToWineDTO(Wine $wine) : WineDTO
    {
        $wineDTO = new WineDTO();
        $wineDTO->setId($wine->getId());
        $wineDTO->setName($wine->getName());
        $wineDTO->setYear($wine->getYear());

        return $wineDTO;
    }
}