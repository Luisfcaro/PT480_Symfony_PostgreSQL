<?php

namespace App\Tests\Integration;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\DTO\Wine\CreateWineDTO;
use App\Repository\WineRepository;
use App\Service\Wine\WineService;
use App\DataFixtures\WineFixtures;
use App\DataFixtures\WineWithMeasurementFixtures;

class WineServiceIntegrationTest extends KernelTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    /** @var WineRepository */
    protected $wineRepository;

    /** @var WineService */
    protected $wineService;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->wineRepository = static::getContainer()->get(WineRepository::class);
        $this->wineService = static::getContainer()->get(WineService::class);
    }

    public function testWineCreatedSucessfully(): void
    {
        $this->databaseTool->loadFixtures([
            WineFixtures::class
        ]);

        $createWineDTO = new CreateWineDTO();
        $createWineDTO->setName('Londres');
        $createWineDTO->setYear(2002);

        $createdWine = $this->wineService->createWine($createWineDTO);

        $this->assertNotNull($createdWine);
        $this->assertEquals('Londres', $createdWine->getName());

        $wineInDatabase = $this->wineRepository->findOneBy(['name' => 'Londres']);

        $this->assertNotNull($wineInDatabase);
    }

    public function testWineCreationFailed(): void
    {
        $this->databaseTool->loadFixtures([
            WineFixtures::class
        ]);

        $createWineDTO = new CreateWineDTO();
        $createWineDTO->setName('The First Wine');
        $createWineDTO->setYear(2020);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('There already exits a wine with that name and year of production');

        $this->wineService->createWine($createWineDTO);
    }

    public function testGetAllWineAndMeasurement(): void
    {
        $this->databaseTool->loadFixtures([
            WineFixtures::class,
            WineWithMeasurementFixtures::class
        ]);

        $wines = $this->wineRepository->findAll();

        $this->assertCount(3, $wines);
    }
}