<?php

namespace App\Service\Sensor;

use App\Repository\SensorRepository;
use App\Service\Sensor\SensorServiceInterface;
use App\Validator\Sensor\CreateSensorValidator;
use App\DTO\Sensor\CreateSensorDTO;
use App\DTO\Sensor\GetAllSensorByNameDTO;
use App\Mapper\Sensor\SensorMapper;
use Doctrine\ORM\EntityManagerInterface;
use App\Validator\Sensor\GetAllSensorByNameValidator;

class SensorService implements SensorServiceInterface
{
    private $sensorRepository;
    private $createSensorValidator;
    private $sensorMapper;
    private $entityManager;
    private $getAllSensorByNameValidator;

    public function __construct(
        CreateSensorValidator $createSensorValidator,
        SensorRepository $sensorRepository,
        SensorMapper $sensorMapper,
        EntityManagerInterface $entityManager,
        GetAllSensorByNameValidator $getAllSensorByNameValidator,
    ) {
        $this->createSensorValidator = $createSensorValidator;
        $this->sensorRepository = $sensorRepository;
        $this->sensorMapper = $sensorMapper;
        $this->entityManager = $entityManager;
        $this->getAllSensorByNameValidator = $getAllSensorByNameValidator;
    }

    public function createSensor(CreateSensorDTO $createSensorDTO)
    {
        $this->createSensorValidator->validateCreateSensorData($createSensorDTO);

        $sensorExist = $this->sensorRepository->findOneBy(['name' => $createSensorDTO->getName()]);

        if ($sensorExist) {
            throw new \Exception("Sensor with that name already exists", 409);
        }

        $sensor = $this->sensorMapper->createSensorDtoToEntity($createSensorDTO);

        $this->entityManager->persist($sensor);
        $this->entityManager->flush();

        $finalSensor = $this->sensorMapper->entityTocreateSensorDto($sensor);

        return $finalSensor;
    }

    public function getAllSensorByName(GetAllSensorByNameDTO $getAllSensorByNameDTO)
    {
        $this->getAllSensorByNameValidator->validateAllSensorByNameData($getAllSensorByNameDTO);

        $sensors = $this->sensorRepository->findAllSensorByName($getAllSensorByNameDTO);

        $allSensorsByName = [];

        foreach ($sensors as $sensor) {
            $allSensorsByName[] = $this->sensorMapper->entityToSensorDTO($sensor);
        }

        return $allSensorsByName;
    }
}