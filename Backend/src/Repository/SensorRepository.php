<?php

namespace App\Repository;

use App\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sensor>
 */
class SensorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensor::class);
    }

//    /**
//     * @return Sensor[] Returns an array of Sensor objects
//     */
   public function find_sensors_by_name($order): array
   {

        if ($order == 0){
            return $this->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
        } else {
            return $this->createQueryBuilder('s')
            ->orderBy('s.name', 'DESC')
            ->getQuery()
            ->getResult();
        }

   }

}
