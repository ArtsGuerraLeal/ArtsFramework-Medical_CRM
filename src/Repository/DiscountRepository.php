<?php

namespace App\Repository;

use App\Entity\Discount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Discount|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discount|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discount[]    findAll()
 * @method Discount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discount::class);
    }

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('discount')
            ->andWhere('discount.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('discount.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('discount')
            ->andWhere('discount.company = :val')
            ->andWhere('discount.id = :id')
            ->setParameter('val', $companyId)
            ->setParameter('id', $id)
            ->orderBy('discount.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return Discount[] Returns an array of Discount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Discount
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
