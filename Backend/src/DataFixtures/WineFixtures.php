<?php

namespace App\DataFixtures;

use App\Entity\Wine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WineFixtures extends Fixture
{
    public const WINE_NUMBER_ONE_REFERENCE = 'first-wine';
    public const WINE_NUMBER_TWO_REFERENCE = 'second-wine';

    public function load(ObjectManager $manager)
    {
        $wine1 = new Wine();
        $wine1->setName('The First Wine');
        $wine1->setYear(2020);

        $wine2 = new Wine();
        $wine2->setName('The Second Wine');
        $wine2->setYear(2004);

        $manager->persist($wine1);
        $manager->persist($wine2);

        $manager->flush();

        $this->addReference(self::WINE_NUMBER_ONE_REFERENCE, $wine1);
        $this->addReference(self::WINE_NUMBER_TWO_REFERENCE, $wine2);
    }
}