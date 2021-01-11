<?php

namespace App\Repository;

use App\Entity\ProviderOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProviderOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProviderOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProviderOrder[]    findAll()
 * @method ProviderOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderOrder::class);
    }

    /**
     * @param $companyId
     * @return ProviderOrder[] Returns an array of ProviderOrder objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('provider_order')
            ->andWhere('provider_order.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('provider_order.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    

    /**
     * @param $companyId
     * @param $id
     * @return ProviderOrder
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('provider_order')
            ->andWhere('provider_order.company = :company')
            ->andWhere('provider_order.id = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('provider_order.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return ProviderOrder[] Returns an array of ProviderOrder objects
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
    public function findOneBySomeField($value): ?ProviderOrder
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
