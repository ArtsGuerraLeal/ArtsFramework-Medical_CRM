<?php

namespace App\Repository;

use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sale|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sale|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sale[]    findAll()
 * @method Sale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }

    /**
     * @param $companyId
     * @return Sale[] Returns an array of Appointment objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('sale')
            ->andWhere('sale.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('sale.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('sale')
            ->andWhere('sale.company = :val')
            ->andWhere('sale.id = :id')
            ->setParameter('val', $companyId)
            ->setParameter('id', $id)
            ->orderBy('sale.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findAllByCompanyColumn($companyId,$parameter)
    {
        return $this->createQueryBuilder('sale')
            ->andWhere('sale.company = :val')
            ->andWhere('sale.'.$parameter.' = :param')
            ->setParameter('val', $companyId)
            ->setParameter('param', $parameter)
            ->orderBy('sale.'.$parameter, 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllByCompanyDate($companyId,$date)
    {
        
        $from = new \DateTime($date->format("Y-m-d")." 00:00:00");
        $to   = new \DateTime($date->format("Y-m-d")." 23:59:59");

        return $this->createQueryBuilder('sale')
            ->andWhere('sale.company = :val')
            ->andWhere('sale.time BETWEEN :from AND :to')
            ->setParameter('val', $companyId)
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult()
            ;
    }


    public function findAllByCompanyMonth($companyId,$date)
    {
        
        $from = new \DateTime($date->format("Y-m-1"));
        $to   = new \DateTime($date->format("Y-m-t"));

        return $this->createQueryBuilder('sale')
            ->andWhere('sale.company = :val')
            ->andWhere('sale.time BETWEEN :from AND :to')
            ->setParameter('val', $companyId)
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllByCompanyDateMonth($companyId,$date,$monthNum)
    {
        
        $from = new \DateTime($date->format("Y-".$monthNum."-1"));
        $to   = new \DateTime($date->format("Y-".$monthNum."-t"));

        return $this->createQueryBuilder('sale')
            ->andWhere('sale.company = :val')
            ->andWhere('sale.time BETWEEN :from AND :to')
            ->setParameter('val', $companyId)
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllByCompanyColumnToday($companyId,$parameter)
    {
        return $this->createQueryBuilder('sale')
            ->andWhere('sale.company = :val')
            ->andWhere('sale.'.$parameter.' = :param')
            ->andWhere('cast(sale.time as Date) = cast(getdate() as Date)')
            ->setParameter('val', $companyId)
            ->setParameter('param', $parameter)
            ->orderBy('sale.'.$parameter, 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Sale[] Returns an array of Sale objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sale
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
