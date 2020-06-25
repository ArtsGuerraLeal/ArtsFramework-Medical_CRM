<?php

namespace App\Repository;

use App\Entity\CustomForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method CustomForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomForm[]    findAll()
 * @method CustomForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomFormRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomForm::class);
    }

    /**
     * @return CustomForm[] Returns an array of Equipment objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('custom_form')
            ->andWhere('custom_form.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('custom_form.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $companyId
     * @param $id
     * @return CustomForm
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('custom_form')
            ->andWhere('custom_form.company = :company')
            ->andWhere('custom_form.id = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('custom_form.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
