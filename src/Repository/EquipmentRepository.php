<?php

namespace App\Repository;

use App\Entity\Equipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

class EquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipment::class);
    }

    /**
     * @return Equipment[] Returns an array of Equipment objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('equipment')
            ->andWhere('equipment.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('equipment.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $companyId
     * @param $id
     * @return Equipment
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('equipment')
            ->andWhere('equipment.company = :company')
            ->andWhere('equipment.id = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('equipment.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
