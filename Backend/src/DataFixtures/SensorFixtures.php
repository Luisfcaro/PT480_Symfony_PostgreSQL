<?php

namespace App\DataFixtures;

use App\Entity\Sensor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SensorFixtures extends Fixture
{
    public const SENSOR_NUMBER_ONE_REFERENCE = 'first-sensor';
    public const SENSOR_NUMBER_TWO_REFERENCE = 'second-sensor';
    
    public function load(ObjectManager $manager)
    {
        $sensor1 = new Sensor();
        $sensor1->setName('Test Sensor 1');

        $sensor2 = new Sensor();
        $sensor2->setName('Test Sensor 2');

        $manager->persist($sensor1);
        $manager->persist($sensor2);

        $manager->flush();

        $this->addReference(self::SENSOR_NUMBER_ONE_REFERENCE, $sensor1);
        $this->addReference(self::SENSOR_NUMBER_TWO_REFERENCE, $sensor2);
    }
}
