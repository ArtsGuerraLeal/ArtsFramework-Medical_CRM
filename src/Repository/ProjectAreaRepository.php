<?php

namespace App\Repository;

use App\Entity\ProjectArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectArea|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectArea|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectArea[]    findAll()
 * @method ProjectArea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectAreaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectArea::class);
    }


    // /**
    //  * @return ProjectArea[] Returns an array of ProjectArea objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProjectArea
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
