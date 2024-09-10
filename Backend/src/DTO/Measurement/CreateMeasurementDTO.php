<?php

namespace App\DTO\Measurement;

class CreateMeasurementDTO
{
    private ?int $id = null;
    private ?int $year = null;
    private ?int $sensorId = null;
    private ?int $wineId = null;
    private ?string $color = null;
    private ?float $temperature = null;
    private ?float $graduation = null;
    private ?float $ph = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    public function getSensorId(): ?int
    {
        return $this->sensorId;
    }

    public function setSensorId(?int $sensorId): void
    {
        $this->sensorId = $sensorId;
    }

    public function getWineId(): ?int
    {
        return $this->wineId;
    }

    public function setWineId(?int $wineId): void
    {
        $this->wineId = $wineId;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(?float $temperature): void
    {
        $this->temperature = $temperature;
    }

    public function getGraduation(): ?float
    {
        return $this->graduation;
    }

    public function setGraduation(?float $graduation): void
    {
        $this->graduation = $graduation;
    }

    public function getPh(): ?float
    {
        return $this->ph;
    }

    public function setPh(?float $ph): void
    {
        $this->ph = $ph;
    }
}