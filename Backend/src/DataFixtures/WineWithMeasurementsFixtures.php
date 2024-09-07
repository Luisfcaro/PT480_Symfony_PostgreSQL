<?php

namespace App\DataFixtures;

use App\Entity\Measurement;
use App\Entity\Wine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\SensorFixtures;

class WineWithMeasurementFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $sensor1 = $this->getReference(SensorFixtures::SENSOR_NUMBER_ONE_REFERENCE);
        $sensor2 = $this->getReference(SensorFixtures::SENSOR_NUMBER_TWO_REFERENCE);

        $wine = new Wine();
        $wine->setName('Chardonnay');
        $wine->setYear(2020);

        $manager->persist($wine);

        $measurement1 = new Measurement();
        $measurement1->setYear(2021)
                     ->setSensorId($sensor1)
                     ->setWineId($wine)
                     ->setColor('Yellow')
                     ->setTemperature(18.0)
                     ->setGraduation(13.0)
                     ->setPh(3.2);

        $measurement2 = new Measurement();
        $measurement2->setYear(2021)
                     ->setSensorId($sensor2)
                     ->setWineId($wine)
                     ->setColor('Yellow')
                     ->setTemperature(19.5)
                     ->setGraduation(12.8)
                     ->setPh(3.1);

        $manager->persist($measurement1);
        $manager->persist($measurement2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SensorFixtures::class,
        ];
    }
}
