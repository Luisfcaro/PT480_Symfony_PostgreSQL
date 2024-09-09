<?php

namespace App\Tests\Integration;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\DTO\Sensor\CreateSensorDTO;
use App\DTO\Sensor\GetAllSensorByNameDTO;
use App\DataFixtures\SensorFixtures;
use App\Repository\SensorRepository;
use App\Service\Sensor\SensorService;

class SensorServiceIntegrationTest extends KernelTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    /** @var SensorRepository */
    protected $sensorRepository;

    /** @var SensorService */
    protected $sensorService;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->sensorRepository = static::getContainer()->get(SensorRepository::class);
        $this->sensorService = static::getContainer()->get(SensorService::class);
    }

    public function testCreateSensorSuccessfully(): void
    {
        $this->databaseTool->loadFixtures([
            SensorFixtures::class
        ]);

        $createSensorDTO = new CreateSensorDTO();
        $createSensorDTO->setName('New Sensor');

        $createdSensor = $this->sensorService->createSensor($createSensorDTO);

        $this->assertNotNull($createdSensor);
        $this->assertEquals('New Sensor', $createdSensor->getName());

        $sensorInDatabase = $this->sensorRepository->findOneBy(['name' => 'New Sensor']);
        $this->assertNotNull($sensorInDatabase);
    }

    public function testCreateSensorWithExistingName(): void
    {
        $this->databaseTool->loadFixtures([
            SensorFixtures::class
        ]);

        $createSensorDTO = new CreateSensorDTO();
        $createSensorDTO->setName('Test Sensor 1');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Sensor with that name already exist');

        $this->sensorService->createSensor($createSensorDTO);
    }

    public function testGetAllSensorByNameSuccessfully(): void
    {
        $this->databaseTool->loadFixtures([
            SensorFixtures::class
        ]);

        $getAllSensorByNameDTO = new GetAllSensorByNameDTO();
        $getAllSensorByNameDTO->setOrder('0');

        $sensors = $this->sensorService->getAllSensorByName($getAllSensorByNameDTO);

        $this->assertCount(2, $sensors);
        $this->assertEquals('Test Sensor 1', $sensors[0]->getName());
        $this->assertEquals('Test Sensor 2', $sensors[1]->getName());
    }
}