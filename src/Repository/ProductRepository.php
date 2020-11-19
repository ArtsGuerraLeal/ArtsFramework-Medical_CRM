<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findArray()
    {
        return $this->createQueryBuilder('product')
            ->orderBy('product.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function countElements($companyId)
    {
        try {
            return $this->createQueryBuilder('product')
                ->select("count(product.id)")
                ->where('product.company = :val')
                ->setParameter('val', $companyId)
                ->orderBy('product.id', 'ASC')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }
    }

    /**
     * @param $start
     * @param $length
     * @param $companyId
     * @return Product[] Returns an array of product objects
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findDataTable($start, $length,$companyId)
    {

        $query = $this->createQueryBuilder('product');
        $countQuery = $this->createQueryBuilder('product');

        $countQuery->select('COUNT(product)');

        $query->select('product.id')
            ->addSelect('product.name')
            ->addSelect('product.price')
            ->addSelect('product.quantity')
            ->addSelect('category.name as category_name')
            ->addSelect('product.image')
            ->addSelect('product.isTaxable')
            ->addSelect('product.isActive')
            ->addSelect('product.isMultiple')
            ->where('product.company = :val')
            ->leftJoin('product.category', 'category');




        $query
            ->setParameter('val', $companyId)
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->orderBy('product.id', 'ASC');

        $results = $query->getQuery()->getArrayResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();

        return array(
            "results" 		=> $results,
            "countResult"	=> $countResult
        );


    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
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
