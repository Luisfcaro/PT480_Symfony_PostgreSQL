<?php

namespace App\Entity;

use App\Repository\MeasurementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeasurementRepository::class)]
class Measurement
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\ManyToOne(inversedBy: 'measurements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sensor $sensor_id = null;

    #[ORM\ManyToOne(inversedBy: 'measurements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wine $wine_id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $color = null;

    #[ORM\Column]
    private ?float $temperature = null;

    #[ORM\Column]
    private ?float $graduation = null;

    #[ORM\Column]
    private ?float $ph = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getSensorId(): ?Sensor
    {
        return $this->sensor_id;
    }

    public function setSensorId(?Sensor $sensor_id): static
    {
        $this->sensor_id = $sensor_id;

        return $this;
    }

    public function getWineId(): ?Wine
    {
        return $this->wine_id;
    }

    public function setWineId(?Wine $wine_id): static
    {
        $this->wine_id = $wine_id;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getGraduation(): ?float
    {
        return $this->graduation;
    }

    public function setGraduation(float $graduation): static
    {
        $this->graduation = $graduation;

        return $this;
    }

    public function getPh(): ?float
    {
        return $this->ph;
    }

    public function setPh(float $ph): static
    {
        $this->ph = $ph;

        return $this;
    }


    public function toArray(): array {
        return [
            'id' => $this->getId(),
            'year' => $this->getYear(),
            'sensor_id' => $this->getSensorId() ? $this->getSensorId()->toArray() : null,
            'wine_id' => $this->getWineId() ? $this->getWineId()->toArray() : null,
            'color' => $this->getColor(),
            'temperature' => $this->getTemperature(),
            'graduation' => $this->getGraduation(),
            'ph' => $this->ph,
        ];
    }

    public function toArrayWithIds(): array {
        return [
            'id' => $this->getId(),
            'year' => $this->getYear(),
            'sensor_id' => $this->getSensorId() ? $this->getSensorId()->getId() : null,
            'wine_id' => $this->getWineId() ? $this->getWineId()->getId() : null,
            'color' => $this->getColor(),
            'temperature' => $this->getTemperature(),
            'graduation' => $this->getGraduation(),
            'ph' => $this->ph,
        ];
    }
}
