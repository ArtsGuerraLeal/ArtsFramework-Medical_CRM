<?php

namespace App\Repository;

use App\Entity\Treatment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Treatment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Treatment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Treatment[]    findAll()
 * @method Treatment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreatmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Treatment::class);
    }

     /**
      * @return Treatment[] Returns an array of Treatment objects
      */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('treatment')
            ->andWhere('treatment.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('treatment.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCompany2($companyId)
    {
        return $this->createQueryBuilder('treatment')
            ->andWhere('treatment.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('treatment.name', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @param $companyId
     * @param $id
     * @return Treatment
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('treatment')
            ->andWhere('treatment.company = :company')
            ->andWhere('treatment.id = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('treatment.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?Treatment
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
