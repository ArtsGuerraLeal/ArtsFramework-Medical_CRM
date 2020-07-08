<?php

namespace App\Repository;

use App\Entity\ProductSold;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductSold|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductSold|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductSold[]    findAll()
 * @method ProductSold[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductSoldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductSold::class);
    }

    // /**
    //  * @return ProductSold[] Returns an array of ProductSold objects
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
    public function findOneBySomeField($value): ?ProductSold
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
