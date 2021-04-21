<?php

namespace App\Repository;

use App\Entity\SimpleAppointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SimpleAppointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method SimpleAppointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method SimpleAppointment[]    findAll()
 * @method SimpleAppointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimpleAppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SimpleAppointment::class);
    }

    // /**
    //  * @return SimpleAppointment[] Returns an array of SimpleAppointment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SimpleAppointment
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
