<?php

namespace App\Repository;

use App\Entity\ProductQuoteDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductQuoteDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductQuoteDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductQuoteDiscount[]    findAll()
 * @method ProductQuoteDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductQuoteDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductQuoteDiscount::class);
    }

    // /**
    //  * @return ProductQuoteDiscount[] Returns an array of ProductQuoteDiscount objects
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
    public function findOneBySomeField($value): ?ProductQuoteDiscount
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
