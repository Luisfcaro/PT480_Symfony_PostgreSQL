<?php

namespace App\Service\Measurement;

use App\DTO\Measurement\CreateMeasurementDTO;
use App\Validator\Measurement\CreateMeasurementValidator;
use App\Service\Measurement\MeasurementServiceInterface;
use App\Repository\MeasurementRepository;
use App\Repository\SensorRepository;
use App\Repository\WineRepository;
use App\Mapper\Measurement\MeasurementMapper;
use Doctrine\ORM\EntityManagerInterface;

class MeasurementService implements MeasurementServiceInterface
{
    private $createMeasurementValidator;
    private $sensorRepository;
    private $measurementRepository;
    private $wineRepository;
    private $measurementMapper;
    private $entityManager;

    public function __construct(
        CreateMeasurementValidator $createMeasurementValidator,
        MeasurementRepository $measurementRepository,
        SensorRepository $sensorRepository,
        WineRepository $wineRepository,
        MeasurementMapper $measurementMapper,
        EntityManagerInterface $entityManager
    ) {
        $this->createMeasurementValidator = $createMeasurementValidator;
        $this->measurementRepository = $measurementRepository;
        $this->sensorRepository = $sensorRepository;
        $this->wineRepository = $wineRepository;
        $this->measurementMapper = $measurementMapper;
        $this->entityManager = $entityManager;
    }

    public function createMeasurement(CreateMeasurementDTO $createMeasurementDTO)
    {
        $this->createMeasurementValidator->validateCreateMeasurementData($createMeasurementDTO);

        $sensor = $this->sensorRepository->findOneBy(['id' => $createMeasurementDTO->getSensorId()]);

        if (!$sensor) {
            throw new \Exception('Sensor referenced does not exists', 409);
        }

        $wine = $this->wineRepository->findOneBy(['id' => $createMeasurementDTO->getWineId()]);

        if (!$wine) {
            throw new \Exception('Wine referenced does not exists', 409);
        }

        if ($createMeasurementDTO->getYear() < $wine->getYear()) {
            throw new \Exception('Year of measurement cant be before wine production year', 403);
        }

        $measurementExist = $this->measurementRepository->findOneBy([
            'sensor_id' => $sensor,
            'wine_id' => $wine,
            'year' => $createMeasurementDTO->getYear()
        ]);

        if ($measurementExist) {
            throw new \Exception("There already exits a measurement with that sensor, on that wine and that year", 409);
        }

        $measurement = $this->measurementMapper->createMeasurementProcessToEntity($createMeasurementDTO, $sensor, $wine);

        $this->entityManager->persist($measurement);
        $this->entityManager->flush();

        $finalMeasurement = $this->measurementMapper->entityToMeasurementDTO($measurement);

        return $finalMeasurement;
    }
}