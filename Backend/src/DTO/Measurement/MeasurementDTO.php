<?php

namespace App\DTO\Measurement;

use App\DTO\Sensor\SensorDTO;
use App\DTO\Wine\WineDTO;

class MeasurementDTO
{
    private int $id;
    private int $year;
    private SensorDTO $sensor;
    private WineDTO $wine;
    private string $color;
    private float $temperature;
    private float $graduation;
    private float $ph;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getSensor(): SensorDTO
    {
        return $this->sensor;
    }

    public function setSensor(SensorDTO $sensor): static
    {
        $this->sensor = $sensor;

        return $this;
    }

    public function getWine(): WineDTO
    {
        return $this->wine;
    }

    public function setWine(WineDTO $wine): static
    {
        $this->wine = $wine;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getGraduation(): float
    {
        return $this->graduation;
    }

    public function setGraduation(float $graduation): static
    {
        $this->graduation = $graduation;

        return $this;
    }

    public function getPh(): float
    {
        return $this->ph;
    }

    public function setPh(float $ph): static
    {
        $this->ph = $ph;

        return $this;
    }
}