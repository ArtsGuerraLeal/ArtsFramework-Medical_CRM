<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @param $companyId
     * @return Category
     * @throws NonUniqueResultException
     */
    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('category')
            ->andWhere('category.company = :company')
            ->setParameter('company', $companyId)
            ->orderBy('category.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $companyId
     * @param $id
     * @return Category
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('category')
            ->andWhere('category.company = :company')
            ->andWhere('category.id = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('category.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param $companyId
     * @param $name
     * @return Category
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyName($companyId,$name)
    {
        return $this->createQueryBuilder('category')
            ->andWhere('category.company = :company')
            ->andWhere('category.name = :name')
            ->setParameter('company', $companyId)
            ->setParameter('name', $name)
            ->orderBy('category.name', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
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
    public function findOneBySomeField($value): ?Category
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
