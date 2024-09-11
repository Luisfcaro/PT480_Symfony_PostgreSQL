<?php

namespace App\Service\Wine;

use App\Entity\Wine;
use App\DTO\Wine\CreateWineDTO;
use App\Service\Wine\WineServiceInterface;
use App\Repository\WineRepository;
use App\Mapper\Wine\WineMapper;
use App\Mapper\Wine\WineMeasurementMapper;
use App\Validator\Wine\CreateWineValidator;
use Doctrine\ORM\EntityManagerInterface;
// use App\Mapper\Measurement\MeasurementMapper;

class WineService implements WineServiceInterface
{
    private $createWineValidator;
    private $wineRepository;
    private $wineMapper;
    private $wineMeasurementMapper;
    private $entityManager;
    // private $measurementMapper;

    public function __construct(
        CreateWineValidator $createWineValidator,
        WineRepository $wineRepository,
        WineMapper $wineMapper,
        WineMeasurementMapper $wineMeasurementMapper,
        EntityManagerInterface $entityManager,
        // MeasurementMapper $measurementMapper
    ) {
        $this->createWineValidator = $createWineValidator;
        $this->wineRepository = $wineRepository;
        $this->wineMapper = $wineMapper;
        $this->wineMeasurementMapper = $wineMeasurementMapper;
        $this->entityManager = $entityManager;
        // $this->measurementMapper = $measurementMapper;
    }

    public function createWine(CreateWineDTO $createWineDTO)
    {
        $this->createWineValidator->validateCreateWineData($createWineDTO);

        $wineExist = $this->wineRepository->findOneBy(['name' => $createWineDTO->getName(), 'year' => $createWineDTO->getYear()]);

        if ($wineExist) {
            throw new \Exception("There already exists a wine with that name and year of production", 409);
        }

        $wine = $this->wineMapper->createWineDTOToEntity($createWineDTO);
        $this->entityManager->persist($wine);
        $this->entityManager->flush();

        $finalWine = $this->wineMapper->entityToWineDTO($wine);

        return $finalWine;
    }

    public function findAllWineWithITSMeasurements()
    {
        $wines = $this->wineRepository->findAll();

        $finalWines = [];

        foreach ($wines as $wine) {
            // $wineDTO = $this->wineMapper->entityToWineDTO($wine);
            // $measurements = $wine->getMeasurements();
            // foreach ($measurements as $measurement ) {
            //     $wineDTO->addMeasurement($this->measurementMapper->entityToMeasurementDTO($measurement));
            // }
            // $finalWines[] = $wineDTO;

            $finalWines[] = $this->wineMeasurementMapper->entityToWineWithMeasurementsDTO($wine);

        }

        return $finalWines;
    }
}