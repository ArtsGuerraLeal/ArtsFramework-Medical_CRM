<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointment[]    findAll()
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    /**
     * @param $companyId
     * @return Appointment[] Returns an array of Appointment objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('appointment')
            ->andWhere('appointment.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('appointment.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $companyId
     * @param $id
     * @return Appointment
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('appointment')
            ->andWhere('appointment.company = :company')
            ->andWhere('appointment.id = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('appointment.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findByCompany2($companyId)
    {
        return $this->createQueryBuilder('appointment')
            ->select('appointment.id')
            ->innerJoin('appointment.treatment','t')
            ->addSelect('t.id')
            ->Where('appointment.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }


    public function findOneByCompanyAndPatient($companyId,$id)
    {
        return $this->createQueryBuilder('appointment')
            ->andWhere('appointment.company = :company')
            ->andWhere('appointment.patient = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('appointment.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    public function findBetweenDates($companyId,$startDate,$endDate)
    {
        return $this->createQueryBuilder('appointment')
            ->addSelect('appointment')
            ->innerJoin('appointment.treatment','t')
            ->addSelect('t.id')
            ->andWhere('appointment.company = :val')
            ->andWhere('appointment.beginAt BETWEEN :start AND :end')
            ->setParameter('val', $companyId)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('appointment.treatment', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }


    // /**
    //  * @return Appointment[] Returns an array of Appointment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Appointment
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
