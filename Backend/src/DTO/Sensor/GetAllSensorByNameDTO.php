<?php

namespace App\DTO\Sensor;

class GetAllSensorByNameDTO
{
    private ?string $order = null;

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function setOrder(?string $order): void
    {
        $this->order = $order;
    }
}