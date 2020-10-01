<?php

namespace App\Repository;

use App\Entity\CreditSale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CreditSale|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditSale|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditSale[]    findAll()
 * @method CreditSale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditSaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreditSale::class);
    }

    // /**
    //  * @return CreditSale[] Returns an array of CreditSale objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CreditSale
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
