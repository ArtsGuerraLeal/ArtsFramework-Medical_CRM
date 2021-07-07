<?php

namespace App\Repository;

use App\Entity\Area;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Area|null find($id, $lockMode = null, $lockVersion = null)
 * @method Area|null findOneBy(array $criteria, array $orderBy = null)
 * @method Area[]    findAll()
 * @method Area[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AreaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Area::class);
    }

    /**
     * @param $companyId
     * @return Area
     * @throws NonUniqueResultException
     */
    public function findOneByCompany($companyId)
    {
        return $this->createQueryBuilder('area')
            ->andWhere('area.company = :company')
            ->setParameter('company', $companyId)
            ->orderBy('area.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $companyId
     * @param $id
     * @return Area
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('area')
            ->andWhere('area.company = :company')
            ->andWhere('area.id = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('area.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param $sku
     * @param $companyId
     * @return Area[] Returns an array of Content objects
     */
    public function searchByName($name,$companyId)
    {
        $searchQuery = 'area.name LIKE \'%' . $name . '%\'';

        return $this->createQueryBuilder('area')
            ->andWhere('area.company = :val')
            ->andWhere($searchQuery)
            ->setParameter('val', $companyId)
            ->orderBy('area.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getArrayResult()
            ;

    }

    // /**
    //  * @return Area[] Returns an array of Area objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Area
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
