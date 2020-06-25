<?php

namespace App\Repository;

use App\Entity\CustomData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CustomData|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomData|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomData[]    findAll()
 * @method CustomData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomData::class);
    }

    /**
     * @return CustomData[] Returns an array of Treatment objects
     */
    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('custom_data')
            ->andWhere('custom_data.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('custom_data.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return CustomData[] Returns an array of CustomData objects
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
    public function findOneBySomeField($value): ?CustomData
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
