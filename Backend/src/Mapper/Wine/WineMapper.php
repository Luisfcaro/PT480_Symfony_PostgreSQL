<?php

namespace App\Mapper\Wine;

use App\Entity\Wine;
use App\DTO\Wine\WineDTO;

class WineMapper
{
    public function entityToWineDTO(Wine $wine) : WineDTO
    {
        $wineDTO = new WineDTO();
        $wineDTO->setId($wine->getId());
        $wineDTO->setName($wine->getName());
        $wineDTO->setYear($wine->getYear());

        return $wineDTO;
    }
}