<?php

namespace App\Repository;

use App\Entity\Address;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class AddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Address::class);
    }

    /**
     * @param $companyId
     * @return Address[] Returns an array of Address objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('address')
            ->andWhere('address.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('address.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('address')
            ->andWhere('address.company = :company')
            ->andWhere('address.id = :id')
            ->setParameters(array(':company'=> $companyId, ':id'=>$id))
            ->orderBy('address.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


}
