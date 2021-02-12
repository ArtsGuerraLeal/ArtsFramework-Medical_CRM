<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }



    public function findByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('client')
            ->andWhere('client.company = :val')
            ->andWhere('client.id = :id')
            ->setParameter('val', $companyId)
            ->setParameter('id', $id)
            ->orderBy('client.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param $name
     * @param $companyId
     * @return Client[] Returns an array of Content objects
     */
    public function findAllByName($name,$companyId)
    {
        return $this->createQueryBuilder('client')
            ->andWhere('client.company = :val')
            ->andWhere('client.name = :val2')
            ->setParameter('val', $companyId)
            ->setParameter('val2', $name)
            ->orderBy('client.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;

    }

    /**
     * @param $name
     * @param $companyId
     * @return Client[] Returns an array of Content objects
     */
    public function searchByName($name,$companyId)
    {
        $searchQuery = 'client.name LIKE \'%' . $name . '%\'';

        return $this->createQueryBuilder('client')
            ->select('client.id')
            ->addSelect('client.name')
            ->addSelect('client.code')
            ->andWhere('client.company = :val')
            ->andWhere($searchQuery)
            ->setParameter('val', $companyId)
            ->orderBy('client.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getArrayResult()
            ;

    }

    

    /**
     * @param $name
     * @param $companyId
     * @return Client[] Returns an array of Content objects
     */
    public function searchOneByName($name,$companyId)
    {

        return $this->createQueryBuilder('client')
            ->andWhere('client.company = :val')
            ->andWhere('client.name = :name')
            ->setParameter('val', $companyId)
            ->setParameter('name', $name)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
            ;

    }

    /**
     * @param $name
     * @param $companyId
     * @return Client[] Returns an array of Content objects
     */
    public function searchOneByCode($code,$companyId)
    {

        return $this->createQueryBuilder('client')
            ->andWhere('client.company = :val')
            ->andWhere('client.code = :code')
            ->setParameter('val', $companyId)
            ->setParameter('code', $code)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
            ;

    }

    /**
     * @param $name
     * @param $companyId
     * @return Client[] Returns an array of Content objects
     */
    public function searchByCode($code,$companyId)
    {
        $searchQuery = 'client.code LIKE \'%' . $code . '%\'';

        return $this->createQueryBuilder('client')
            ->select('client.id')
            ->addSelect('client.name')
            ->addSelect('client.code')
            ->andWhere('client.company = :val')
            ->andWhere($searchQuery)
            ->setParameter('val', $companyId)
            ->orderBy('client.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getArrayResult()
            ;

    }

    /**
     * @param $companyId
     * @return Client[] Returns an array of Content objects
     */
    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('client')
            ->andWhere('client.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('client.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }




}
