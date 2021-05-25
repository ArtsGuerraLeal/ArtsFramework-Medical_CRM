<?php

namespace App\Repository;

use App\Entity\EventTreatment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventTreatment|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventTreatment|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventTreatment[]    findAll()
 * @method EventTreatment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventTreatmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventTreatment::class);
    }

    // /**
    //  * @return EventTreatment[] Returns an array of EventTreatment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventTreatment
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
