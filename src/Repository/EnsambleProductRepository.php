<?php

namespace App\Repository;

use App\Entity\EnsambleProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnsambleProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnsambleProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnsambleProduct[]    findAll()
 * @method EnsambleProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnsambleProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnsambleProduct::class);
    }

    // /**
    //  * @return EnsambleProduct[] Returns an array of EnsambleProduct objects
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
    public function findOneBySomeField($value): ?EnsambleProduct
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
