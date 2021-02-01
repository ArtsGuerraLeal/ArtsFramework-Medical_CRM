<?php

namespace App\Repository;

use App\Entity\ProductSold;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductSold|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductSold|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductSold[]    findAll()
 * @method ProductSold[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductSoldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductSold::class);
    }

    /**
     * @param $companyId
     * @return ProductSold[] Returns an array of ProductSOld objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('productSold')
            ->andWhere('productSold.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('productSold.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('productSold')
            ->andWhere('productSold.company = :val')
            ->andWhere('productSold.id = :id')
            ->setParameter('val', $companyId)
            ->setParameter('id', $id)
            ->orderBy('productSold.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findAllByCompanyMonthMostSold($companyId,$date)
    {
        
        $from = new \DateTime($date->format("Y-m-1"));
        $to   = new \DateTime($date->format("Y-m-t"));

        return $this->createQueryBuilder('productSold')
            ->andWhere('productSold.company = :val')
         //  ->andWhere('productSold.sale = 264')
            ->setParameter('val', $companyId)

            ->getQuery()
            ->getResult()
            ;
    }
    public function GetMostSoldProductsDay($companyId,$date){
  
        $from = new \DateTime($date->format("Y-m-d")." 00:00:00");
        $to   = new \DateTime($date->format("Y-m-d")." 23:59:59");

        return $this->createQueryBuilder('productSold')
            ->Select('count(productSold.product) as prodCount')
            ->addSelect('product.name as prodName' )
            ->addSelect('product.price as prodPrice' )
            ->addSelect('product.id as prodId' )
            ->andWhere('productSold.company = :val')
            ->andWhere('sale.time BETWEEN :from AND :to')
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->Join('productSold.sale', 'sale')
            ->Join('productSold.product', 'product')
            ->setParameter('val', $companyId)
            ->groupBy('product.name')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }

    public function GetMostSoldProducts($companyId,$date){
  
        $from = new \DateTime($date->format("Y-m-1"));
        $to   = new \DateTime($date->format("Y-m-t"));

        return $this->createQueryBuilder('productSold')
            ->Select('count(productSold.product) as prodCount')
            ->addSelect('product.name as prodName' )
            ->addSelect('product.price as prodPrice' )
            ->addSelect('product.id as prodId' )
            ->andWhere('productSold.company = :val')
            ->andWhere('sale.time BETWEEN :from AND :to')
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->Join('productSold.sale', 'sale')
            ->Join('productSold.product', 'product')
            ->setParameter('val', $companyId)
            ->groupBy('product.name')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return ProductSold[] Returns an array of ProductSold objects
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
    public function findOneBySomeField($value): ?ProductSold
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
