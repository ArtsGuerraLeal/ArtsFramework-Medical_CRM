<?php

namespace App\Repository;

use App\Entity\StaffPositions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method StaffPositions|null find($id, $lockMode = null, $lockVersion = null)
 * @method StaffPositions|null findOneBy(array $criteria, array $orderBy = null)
 * @method StaffPositions[]    findAll()
 * @method StaffPositions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaffPositionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaffPositions::class);
    }


    /**
     * @return StaffPositions[] Returns an array of Staff objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('staffPositions')
            ->andWhere('staffPositions.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('staffPositions.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $companyId
     * @param $id
     * @return StaffPositions
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('staffPositions')
            ->andWhere('staffPositions.company = :company')
            ->andWhere('staffPositions.id = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('staffPositions.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?StaffPositions
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
