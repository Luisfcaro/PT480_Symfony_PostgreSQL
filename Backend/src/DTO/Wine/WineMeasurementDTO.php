<?php

namespace App\DTO\Wine;

use App\DTO\Measurement\MeasurementDTO;

class WineMeasurementDTO
{
    private int $id;
    private string $name;
    private int $year;
    private ?array $measurements;

    public function __construct()
    {
        $this->measurements = [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getMeasurements(): array
    {
        return $this->measurements ?? [];
    }

    public function setMeasurements(array $measurements): self
    {
        $this->measurements = $measurements;
        return $this;
    }

    public function addMeasurement(MeasurementDTO $measurement): self
    {
        $this->measurements[] = $measurement;
        return $this;
    }
}