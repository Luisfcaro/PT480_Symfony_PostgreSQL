<?php

namespace App\Tests\Unit\Measurement\Service;

use PHPUnit\Framework\TestCase;
use App\Service\Measurement\MeasurementService;
use App\Repository\MeasurementRepository;
use App\Repository\SensorRepository;
use App\Repository\WineRepository;
use App\Mapper\Measurement\MeasurementMapper;
use App\Validator\Measurement\CreateMeasurementValidator;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Measurement;
use App\Entity\Sensor;
use App\Entity\Wine;
use App\DTO\Measurement\CreateMeasurementDTO;
use App\DTO\Measurement\MeasurementDTO;

class MeasurementServiceTest extends TestCase
{
    private $measurementService;
    private $measurementRepositoryMock;
    private $sensorRepositoryMock;
    private $wineRepositoryMock;
    private $measurementMapperMock;
    private $createMeasurementValidatorMock;
    private $entityManagerMock;

    protected function setUp(): void
    {
        $this->measurementRepositoryMock = $this->createMock(MeasurementRepository::class);
        $this->sensorRepositoryMock = $this->createMock(SensorRepository::class);
        $this->wineRepositoryMock = $this->createMock(WineRepository::class);
        $this->measurementMapperMock = $this->createMock(MeasurementMapper::class);
        $this->createMeasurementValidatorMock = $this->createMock(CreateMeasurementValidator::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->measurementService = new MeasurementService(
            $this->createMeasurementValidatorMock,
            $this->measurementRepositoryMock,
            $this->sensorRepositoryMock,
            $this->wineRepositoryMock,
            $this->measurementMapperMock,
            $this->entityManagerMock
        );
    }

    public function testCreateMeasurementSuccess()
    {
        $createMeasurementDTO = new CreateMeasurementDTO();
        $createMeasurementDTO->setYear(2022);
        $createMeasurementDTO->setSensorId(1);
        $createMeasurementDTO->setWineId(1);
        $createMeasurementDTO->setColor('blue');
        $createMeasurementDTO->setTemperature(2.1);
        $createMeasurementDTO->setGraduation(1.1);
        $createMeasurementDTO->setPh(0.9);

        $this->createMeasurementValidatorMock
            ->expects($this->once())
            ->method('validateCreateMeasurementData')
            ->with($this->equalTo($createMeasurementDTO));

        $sensor = new Sensor();
        $sensor->setName('Sensor 1');

        $this->sensorRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($sensor);

        $wine = new Wine();
        $wine->setName('El primero');
        $wine->setYear(2020);

        $this->wineRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($wine);

        $this->measurementRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['year' => 2022, 'sensor_id' => $sensor, 'wine_id' => $wine])
            ->willReturn(null);
        
        $measurementEntity = new Measurement();
        $measurementEntity->setYear(2022);
        $measurementEntity->setSensorId($sensor);
        $measurementEntity->setWineId($wine);
        $measurementEntity->setColor('blue');
        $measurementEntity->setTemperature(2.1);
        $measurementEntity->setGraduation(1.1);
        $measurementEntity->setPh(0.9);

        $this->measurementMapperMock
            ->expects($this->once())
            ->method('createMeasurementProcessToEntity')
            ->with($this->equalTo($createMeasurementDTO), $this->equalTo($sensor), $this->equalTo($wine))
            ->willReturn($measurementEntity);
        
        $finalMeasurement = new MeasurementDTO();

        $this->measurementMapperMock
            ->expects($this->once())
            ->method('entityToMeasurementDTO')
            ->with($this->equalTo($measurementEntity))
            ->willReturn($finalMeasurement);

        $result = $this->measurementService->createMeasurement($createMeasurementDTO);

        $this->assertSame($finalMeasurement, $result);
    }

    public function testCreateMeasurementFailByProductionYear()
    {
        $createMeasurementDTO = new CreateMeasurementDTO();
        $createMeasurementDTO->setYear(2019);
        $createMeasurementDTO->setSensorId(1);
        $createMeasurementDTO->setWineId(1);
        $createMeasurementDTO->setColor('blue');
        $createMeasurementDTO->setTemperature(2.1);
        $createMeasurementDTO->setGraduation(1.1);
        $createMeasurementDTO->setPh(0.9);

        $sensor = new Sensor();
        $sensor->setName('Sensor 1');

        $this->sensorRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($sensor);

        $wine = new Wine();
        $wine->setName('El primero');
        $wine->setYear(2020);

        $this->wineRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($wine);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Year of measurement cant be before wine production year');

        $this->measurementService->createMeasurement($createMeasurementDTO);
    }

    public function testCreateMeasurementSensorNotExist()
    {
        $createMeasurementDTO = new CreateMeasurementDTO();
        $createMeasurementDTO->setYear(2019);
        $createMeasurementDTO->setSensorId(1);
        $createMeasurementDTO->setWineId(1);
        $createMeasurementDTO->setColor('blue');
        $createMeasurementDTO->setTemperature(2.1);
        $createMeasurementDTO->setGraduation(1.1);
        $createMeasurementDTO->setPh(0.9);

        $this->sensorRepositoryMock
        ->expects($this->once())
        ->method('findOneBy')
        ->with(['id' => 1])
        ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Sensor referenced does not exist');

        $this->measurementService->createMeasurement($createMeasurementDTO);
    }

    public function testCreateMeasurementWineNotExist()
    {
        $createMeasurementDTO = new CreateMeasurementDTO();
        $createMeasurementDTO->setYear(2019);
        $createMeasurementDTO->setSensorId(1);
        $createMeasurementDTO->setWineId(1);
        $createMeasurementDTO->setColor('blue');
        $createMeasurementDTO->setTemperature(2.1);
        $createMeasurementDTO->setGraduation(1.1);
        $createMeasurementDTO->setPh(0.9);

        $sensor = new Sensor();
        $sensor->setName('Sensor 1');

        $this->sensorRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($sensor);


        $this->wineRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wine referenced does not exist');

        $this->measurementService->createMeasurement($createMeasurementDTO);
    }

    public function testCreateMeasurementAlreadyExist(){
        $createMeasurementDTO = new CreateMeasurementDTO();
        $createMeasurementDTO->setYear(2020);
        $createMeasurementDTO->setSensorId(1);
        $createMeasurementDTO->setWineId(1);
        $createMeasurementDTO->setColor('blue');
        $createMeasurementDTO->setTemperature(2.1);
        $createMeasurementDTO->setGraduation(1.1);
        $createMeasurementDTO->setPh(0.9);

        $sensor = new Sensor();
        $sensor->setName('Sensor 1');

        $this->sensorRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($sensor);

        $wine = new Wine();
        $wine->setName('El primero');
        $wine->setYear(2020);

        $this->wineRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($wine);

        $measurementEntity = new Measurement();
        $measurementEntity->setYear(2020);
        $measurementEntity->setSensorId($sensor);
        $measurementEntity->setWineId($wine);
        $measurementEntity->setColor('blue');
        $measurementEntity->setTemperature(2.1);
        $measurementEntity->setGraduation(1.1);
        $measurementEntity->setPh(0.9);

        $this->measurementRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['year' => 2020, 'sensor_id' => $sensor, 'wine_id' => $wine])
            ->willReturn($measurementEntity);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('There already exits a measurement with that sensor, on that wine and that year');

        $this->measurementService->createMeasurement($createMeasurementDTO);
    }
}