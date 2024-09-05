<?php

namespace App\Tests\Unit\Sensor\Service;

use App\Service\Sensor\SensorService;
use App\DTO\Sensor\CreateSensorDTO;
use App\DTO\Sensor\GetAllSensorByNameDTO;
use App\DTO\Sensor\SensorDTO;
use App\Entity\Sensor;
use App\Validator\Sensor\CreateSensorValidator;
use App\Validator\Sensor\GetAllSensorByNameValidator;
use App\Repository\SensorRepository;
use App\Mapper\Sensor\SensorMapper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SensorServiceTest extends TestCase
{
    private $sensorService;
    private $createSensorValidatorMock;
    private $sensorRepositoryMock;
    private $sensorMapperMock;
    private $entityManagerMock;
    private $getAllSensorByNameValidatorMock;

    protected function setUp(): void
    {
        $this->createSensorValidatorMock = $this->createMock(CreateSensorValidator::class);
        $this->getAllSensorByNameValidatorMock = $this->createMock(GetAllSensorByNameValidator::class);
        $this->sensorRepositoryMock = $this->createMock(SensorRepository::class);
        $this->sensorMapperMock = $this->createMock(SensorMapper::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->sensorService = new SensorService(
            $this->createSensorValidatorMock,
            $this->sensorRepositoryMock,
            $this->sensorMapperMock,
            $this->entityManagerMock,
            $this->getAllSensorByNameValidatorMock,
        );
    }

    public function testCreateSensorSuccess()
    {
        $dto = new CreateSensorDTO();
        $dto->setName('Temperature Sensor');

        $sensorEntity = new Sensor();
        $sensorEntity->setName('Temperature Sensor');

        $this->createSensorValidatorMock
            ->expects($this->once())
            ->method('validateCreateSensorData')
            ->with($dto);

        $this->sensorRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Temperature Sensor'])
            ->willReturn(null);

        $this->sensorMapperMock
            ->expects($this->once())
            ->method('createSensorDtoToEntity')
            ->with($dto)
            ->willReturn($sensorEntity);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($sensorEntity);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->sensorMapperMock
            ->expects($this->once())
            ->method('entityTocreateSensorDto')
            ->with($sensorEntity)
            ->willReturn($dto);

        $result = $this->sensorService->createSensor($dto);

        $this->assertSame($dto, $result);
    }

    public function testCreateSensorAlreadyExists()
    {
        $dto = new CreateSensorDTO();
        $dto->setName('Temperature Sensor');

        $existingSensor = new Sensor();
        $existingSensor->setName('Temperature Sensor');

        $this->createSensorValidatorMock
            ->expects($this->once())
            ->method('validateCreateSensorData')
            ->with($dto);

        $this->sensorRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Temperature Sensor'])
            ->willReturn($existingSensor);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Sensor with that name already exist");

        $this->sensorService->createSensor($dto);
    }

    public function testGetAllSensorByNameSuccess()
    {
        $getAllSensorByNameDTO = new GetAllSensorByNameDTO();
        $getAllSensorByNameDTO->setOrder('1');

        $this->getAllSensorByNameValidatorMock
            ->expects($this->once())
            ->method('validateAllSensorByNameData')
            ->with($this->equalTo($getAllSensorByNameDTO));

        $sensorEntity1 = new Sensor();
        $sensorEntity1->setName('Sensor 1');
        $sensorEntity2 = new Sensor();
        $sensorEntity2->setName('Sensor 2');

        $this->sensorRepositoryMock
            ->expects($this->once())
            ->method('findAllSensorByName')
            ->with($this->equalTo($getAllSensorByNameDTO))
            ->willReturn([$sensorEntity1, $sensorEntity2]);

        $sensorDTO1 = new SensorDTO();
        $sensorDTO1->setName('Sensor 1');
        $sensorDTO2 = new SensorDTO();
        $sensorDTO2->setName('Sensor 2');

        $this->sensorMapperMock
            ->expects($this->exactly(2))
            ->method('entityToSensorDTO')
            ->withConsecutive([$sensorEntity1], [$sensorEntity2])
            ->willReturnOnConsecutiveCalls($sensorDTO1, $sensorDTO2);

        $result = $this->sensorService->getAllSensorByName($getAllSensorByNameDTO);

        $this->assertCount(2, $result);
        $this->assertSame('Sensor 1', $result[0]->getName());
        $this->assertSame('Sensor 2', $result[1]->getName());
    }

    public function testGetAllSensorByNameValidationFails()
    {
        $getAllSensorByNameDTO = new GetAllSensorByNameDTO();
        $getAllSensorByNameDTO->setOrder('1');

        $this->getAllSensorByNameValidatorMock
            ->expects($this->once())
            ->method('validateAllSensorByNameData')
            ->with($this->equalTo($getAllSensorByNameDTO))
            ->willThrowException(new \Exception("Validation failed"));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Validation failed");

        $this->sensorService->getAllSensorByName($getAllSensorByNameDTO);
    }
}

