<?php

namespace App\Repository;

use App\Entity\ProductQuote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductQuote|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductQuote|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductQuote[]    findAll()
 * @method ProductQuote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductQuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductQuote::class);
    }

    // /**
    //  * @return ProductQuote[] Returns an array of ProductQuote objects
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
    public function findOneBySomeField($value): ?ProductQuote
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
