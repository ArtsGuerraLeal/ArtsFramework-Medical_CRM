<?php

namespace App\Repository;

use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findRecentByCompany($companyId)
    {
        return $this->createQueryBuilder('sale')
            ->andWhere('sale.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('sale.id', 'DESC')
            ->setMaxResults(10)
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
            ->andWhere('sale.isPaid = 1')
            ->andWhere('sale.isCancelled is null')
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

    public function findAllByCompanyToday($companyId)
    {

        
        return $this->createQueryBuilder('sale')
            ->andWhere('sale.company = :val')
            ->andWhere('cast(sale.time as Date) = cast(getdate() as Date)')
            ->setParameter('val', $companyId)
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

    public function GetMostFrequentClientMonth($companyId,$date){
  
        $from = new \DateTime($date->format("Y-m-1"));
        $to   = new \DateTime($date->format("Y-m-t"));

        return $this->createQueryBuilder('sale')
            ->Select('count(sale.client) as clientCount')
            ->addSelect('sale.client as clientName')
            ->addSelect('sum(sale.total) as clientTotal')
            ->andWhere('sale.company = :val')
            ->andWhere('sale.time BETWEEN :from AND :to')
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->setParameter('val', $companyId)
            ->groupBy('sale.client')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }

    public function GetSalesPerMonth($companyId,$date){
  
        $from = new \DateTime($date->format("2020-1-1"));
        $to   = new \DateTime($date->format("2021-12-31"));

        return $this->createQueryBuilder('sale')
            ->Select('month(sale.time) as saleMonth')
           // ->addSelect('year(sale.time) as saleYear')
            ->addSelect('sum(sale.total) as saleTotal')
            ->andWhere('sale.company = :val')
            ->andWhere('sale.time is not null')
            ->andWhere('year(sale.time) = year(:date)')
            ->setParameter('val', $companyId)
            ->setParameter('date', $date)
            ->groupBy('saleMonth')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult()
            ;
    }

    public function countElements($companyId)
    {
        try {
            return $this->createQueryBuilder('sale')
                ->select("count(sale.id)")
                ->where('sale.company = :val')
                ->setParameter('val', $companyId)
                ->orderBy('sale.id', 'DESC')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }
    }

    /**
     * @param $start
     * @param $length
     * @param $search
     * @param $orders
     * @param $columns
     * @param $companyId
     * @return Sale[] Returns an array of Patient objects
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findDataTable($start, $length,$search,$orders,$columns,$companyId)
    {

        $query = $this->createQueryBuilder('sale');
        $countQuery = $this->createQueryBuilder('sale');

        $countQuery->select('COUNT(sale)');

            $query->select('sale.id')
                ->addSelect('sale.isPaid')
                ->addSelect('sale.isCancelled')
                ->addSelect('sale.client')
                ->addSelect('sale.total')
                ->addSelect('sale.commission')
                ->addSelect('sale.time')
                ->where('sale.company = :val');

        $searchQuery = null;

        if ($search['value'] != '') {


            if(is_numeric($search['value'])){

                    $searchItem = $search['value'];
                    $searchQuery = 'sale.id LIKE \'%' . $searchItem . '%\'';
                
            }elseif(!is_numeric($search['value'])){

                if(strpos($search['value'],'-')){
                    $searchItem = $search['value'];
                    $searchQuery = 'sale.time LIKE \'%' . $searchItem . '%\'';
                }else{
                    $searchItem = $search['value'];
                    $searchQuery = 'sale.client LIKE \'%' . $searchItem. '%\'';
                }

                

            }
        }


        if ($searchQuery !== null) {
            $query->andWhere($searchQuery);
            $countQuery->andWhere($searchQuery);
        }

/*
        foreach ($columns as $key => $column)
        {
            if ($search['value'] != '') {

                // $searchItem is what we are looking for
                $searchItem = $search['value'];
                $searchQuery = null;

                // $column['name'] is the name of the column as sent by the JS
                switch ($column['data']) {
                    case 'id':
                    {
                        $searchQuery = 'patient.id LIKE \'%' . $searchItem . '%\'';
                        break;
                    }
                    case 'firstName':
                    {
                        $searchQuery = 'patient.firstName LIKE \'%' . $searchItem . '%\'';
                        break;
                    }
                    case 'lastName':
                    {
                        $searchQuery = 'patient.lastName LIKE \'%' . $searchItem . '%\'';
                        break;
                    }
                }

                if ($searchQuery !== null) {
                    $query->andWhere($searchQuery);
                    $countQuery->andWhere($searchQuery);
                }
            }
        }

       */

            $query
            ->setParameter('val', $companyId)
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->orderBy('sale.id', 'DESC');

        $results = $query->getQuery()->getArrayResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();

        return array(
            "results" 		=> $results,
            "countResult"	=> $countResult
        );


    }


    public function GetMostSoldProducts(){

        // return $this->createQueryBuilder('sale')
        //     ->addSelect('product_sold.id as prod_id')
        //     ->andWhere('sale.company = :val')
        //     ->Join('product_sold.amount', 'product_sold')
        //     ->setParameter('val', 3)
        //     ->getQuery()
        //     ->getResult()
        //     ;

        //  $query = $this->createQueryBuilder('sale');

        //  $query
        //  ->Select('count(productSold.product)')
        //  ->Join('productSold.sale', 'sale');

        //  return $query
        //  ->getQuery()
        //  ->getResult()
        //  ;

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
