<?php

namespace App\Tests\Integration;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\DTO\Measurement\CreateMeasurementDTO;
use App\Repository\MeasurementRepository;
use App\Service\Measurement\MeasurementService;
use App\DataFixtures\MeasurementFixtures;

class MeasurementServiceIntegrationTest extends KernelTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    /** @var MeasurementRepository */
    protected $measurementRepository;
 
    /** @var MeasurementService */
    protected $measurementService;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->measurementRepository = static::getContainer()->get(MeasurementRepository::class);
        $this->measurementService = static::getContainer()->get(MeasurementService::class);
    }

    public function testMeasurementCreatedSucessfully(): void
    {
        $this->databaseTool->loadFixtures([
            MeasurementFixtures::class
        ]);

        $createMeasurementDTO = new CreateMeasurementDTO();
        $createMeasurementDTO->setYear(2022);
        $createMeasurementDTO->setSensorId(1);
        $createMeasurementDTO->setWineId(1);
        $createMeasurementDTO->setColor('blue');
        $createMeasurementDTO->setTemperature(2.1);
        $createMeasurementDTO->setGraduation(1.1);
        $createMeasurementDTO->setPh(0.9);

        $createdMeasurement = $this->measurementService->createMeasurement($createMeasurementDTO);

        $this->assertNotNull($createdMeasurement);
        $this->assertEquals(2022, $createdMeasurement->getYear());

        $measurementInDatabase = $this->measurementRepository->findOneBy(['year' => 2022]);

        $this->assertNotNull($measurementInDatabase);
    }

    public function testMeasurementCreationFailed(): void
    {
        $this->databaseTool->loadFixtures([
            MeasurementFixtures::class
        ]);

        $createMeasurementDTO = new CreateMeasurementDTO();
        $createMeasurementDTO->setYear(2021);
        $createMeasurementDTO->setSensorId(1);
        $createMeasurementDTO->setWineId(1);
        $createMeasurementDTO->setColor('blue');
        $createMeasurementDTO->setTemperature(2.1);
        $createMeasurementDTO->setGraduation(1.1);
        $createMeasurementDTO->setPh(0.9);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('There already exits a measurement with that sensor, on that wine and that year');

        $this->measurementService->createMeasurement($createMeasurementDTO);
    }
}