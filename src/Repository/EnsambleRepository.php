<?php

namespace App\Repository;

use App\Entity\Ensamble;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ensamble|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ensamble|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ensamble[]    findAll()
 * @method Ensamble[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnsambleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ensamble::class);
    }

    // /**
    //  * @return Ensamble[] Returns an array of Ensamble objects
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
    public function findOneBySomeField($value): ?Ensamble
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
