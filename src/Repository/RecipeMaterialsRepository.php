<?php

namespace App\Repository;

use App\Entity\RecipeMaterials;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecipeMaterials|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipeMaterials|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipeMaterials[]    findAll()
 * @method RecipeMaterials[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeMaterialsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeMaterials::class);
    }

    // /**
    //  * @return RecipeMaterials[] Returns an array of RecipeMaterials objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RecipeMaterials
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
