<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[] Returns an array of User objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('user.registerdate', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    /**
     * @param $companyId
     * @param $username
     * @return User
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyUsername($companyId,$username)
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.company = :company')
            ->andWhere('user.username = :username')
            ->setParameter('company', $companyId)
            ->setParameter('username', $username)
            ->orderBy('user.username', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
