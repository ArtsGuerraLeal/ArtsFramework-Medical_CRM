<?php

namespace App\Repository;

use App\Entity\Vendor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vendor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vendor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vendor[]    findAll()
 * @method Vendor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vendor::class);
    }

     /**
     * @param $companyId
     * @return Vendor
     * @throws NonUniqueResultException
     */
    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('vendor')
            ->andWhere('vendor.company = :company')
            ->setParameter('company', $companyId)
            ->orderBy('vendor.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $companyId
     * @param $id
     * @return Vendor
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('vendor')
            ->andWhere('vendor.company = :company')
            ->andWhere('vendor.id = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('vendor.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param $companyId
     * @param $name
     * @return Vendor
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyName($companyId,$name)
    {
        return $this->createQueryBuilder('vendor')
            ->andWhere('vendor.company = :company')
            ->andWhere('vendor.name = :name')
            ->setParameter('company', $companyId)
            ->setParameter('name', $name)
            ->orderBy('vendor.name', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return Vendor[] Returns an array of Vendor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Vendor
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
