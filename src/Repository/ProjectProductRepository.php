<?php

namespace App\Repository;

use App\Entity\ProjectProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectProduct[]    findAll()
 * @method ProjectProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectProduct::class);
    }

    public function findOneByCompanyID($companyId,$id){
  
        return $this->createQueryBuilder('projectProduct')
            ->andWhere('projectProduct.company = :val')
            ->andWhere('projectProduct.id = :id')
            ->setParameter('id', $id )
            ->setParameter('val', $companyId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function FindProductsInArea($companyId,$areaID,$projectID){
  
        return $this->createQueryBuilder('projectProduct')
            ->Select('projectProduct.id')
            ->addSelect('projectProduct.amount')
            ->addSelect('projectProduct.price')
            ->addSelect('product.id as prodID')
            ->addSelect('product.name as prodName')
            ->addSelect('area.id as areaID')
            ->andWhere('projectProduct.company = :val')
            ->andWhere('projectProduct.area = :areaid')
            ->andWhere('projectProduct.project = :projectid')
            ->setParameter('areaid', $areaID )
            ->setParameter('projectid', $projectID )
            ->Join('projectProduct.product', 'product')
            ->Join('projectProduct.area', 'area')

            ->setParameter('val', $companyId)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    // /**
    //  * @return ProjectProduct[] Returns an array of ProjectProduct objects
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
    public function findOneBySomeField($value): ?ProjectProduct
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
