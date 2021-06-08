<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('event')
            ->andWhere('event.company = :val')
            ->andWhere('event.id = :id')
            ->setParameter('val', $companyId)
            ->setParameter('id', $id)
            ->orderBy('event.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findAllByCompanyDate($companyId,$start,$end)
    {
        
        $from = new \DateTime($start->format("Y-m-d")." 00:00:00");
        $to   = new \DateTime($end->format("Y-m-d")." 23:59:59");

        return $this->createQueryBuilder('event')
            ->Select('event.id')
            ->addSelect('event.start')
            ->addSelect('event.end')
            ->addSelect('event.title')
            ->addSelect('calendar.color as color')
            ->addSelect('client.name as clientName')
            ->andWhere('event.company = :val')
            ->andWhere('event.start BETWEEN :from AND :to')
            ->Join('event.calendar', 'calendar')
            ->Join('event.client', 'client')
            ->setParameter('val', $companyId)
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
