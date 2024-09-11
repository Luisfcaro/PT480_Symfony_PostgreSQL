<?php

namespace App\Tests\Unit\Wine\Service;

use PHPUnit\Framework\TestCase;
use App\Service\Wine\WineService;
use App\Validator\Wine\CreateWineValidator;
use App\Repository\WineRepository;
use App\Mapper\Wine\WineMapper;
use App\Mapper\Wine\WineMeasurementMapper;
use Doctrine\ORM\EntityManagerInterface;
use App\DTO\Wine\CreateWineDTO;
use App\DTO\Wine\WineDTO;
use App\DTO\Wine\WineMeasurementDTO;
use App\Entity\Wine;

class WineServiceTest extends TestCase
{
    private $wineService;
    private $createWineValidatorMock;
    private $wineRepositoryMock;
    private $wineMapperMock;
    private $wineMeasurementMapperMock;
    private $entityManagerMock;

    protected function setUp(): void
    {
        $this->createWineValidatorMock = $this->createMock(CreateWineValidator::class);
        $this->wineRepositoryMock = $this->createMock(WineRepository::class);
        $this->wineMapperMock = $this->createMock(WineMapper::class);
        $this->wineMeasurementMapperMock = $this->createMock(WineMeasurementMapper::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->wineService = new WineService(
            $this->createWineValidatorMock,
            $this->wineRepositoryMock,
            $this->wineMapperMock,
            $this->wineMeasurementMapperMock,
            $this->entityManagerMock
        );
    }

    public function testCreateWineSuccess()
    {
        $createWineDTO = new CreateWineDTO();
        $createWineDTO->setName('Wine 1');
        $createWineDTO->setYear(2022);

        $this->createWineValidatorMock
            ->expects($this->once())
            ->method('validateCreateWineData')
            ->with($this->equalTo($createWineDTO));

        $this->wineRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Wine 1', 'year' => 2022])
            ->willReturn(null);

        $wineEntity = new Wine();
        $wineEntity->setName('Wine 1');
        $wineEntity->setYear(2022);

        $this->wineMapperMock
            ->expects($this->once())
            ->method('createWineDTOToEntity')
            ->with($this->equalTo($createWineDTO))
            ->willReturn($wineEntity);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($wineEntity));

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $finalWineDTO = new WineDTO();
        $finalWineDTO->setName('Wine 1');
        $finalWineDTO->setYear(2022);
        
        $this->wineMapperMock
            ->expects($this->once())
            ->method('entityToWineDTO')
            ->with($this->equalTo($wineEntity))
            ->willReturn($finalWineDTO);

        $result = $this->wineService->createWine($createWineDTO);

        $this->assertSame($finalWineDTO, $result);
    }

    public function testCreateWineAlreadyExists()
    {
        $createWineDTO = new CreateWineDTO();
        $createWineDTO->setName('Wine 1');
        $createWineDTO->setYear(2022);

        $wineEntity = new Wine();
        $wineEntity->setName('Wine 1');
        $wineEntity->setYear(2022);

        $this->wineRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Wine 1', 'year' => 2022])
            ->willReturn($wineEntity);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("There already exists a wine with that name and year of production");

        $this->wineService->createWine($createWineDTO);
    }

    public function testFindAllWineWithITSMeasurementsSuccess()
    {
        $wineEntity1 = new Wine();
        $wineEntity2 = new Wine();

        $this->wineRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$wineEntity1, $wineEntity2]);

        $wineWithMeasurementsDTO1 = new WineMeasurementDTO();
        $wineWithMeasurementsDTO2 = new WineMeasurementDTO();

        $this->wineMeasurementMapperMock
            ->expects($this->exactly(2))
            ->method('entityToWineWithMeasurementsDTO')
            ->withConsecutive([$wineEntity1], [$wineEntity2])
            ->willReturnOnConsecutiveCalls($wineWithMeasurementsDTO1, $wineWithMeasurementsDTO2);

        $result = $this->wineService->findAllWineWithITSMeasurements();

        $this->assertCount(2, $result);
        $this->assertSame($wineWithMeasurementsDTO1, $result[0]);
        $this->assertSame($wineWithMeasurementsDTO2, $result[1]);
    }
}