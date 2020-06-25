<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }

    public function countElements($companyId)
    {
        try {
            return $this->createQueryBuilder('patient')
                ->select("count(patient.id)")
                ->where('patient.company = :val')
                ->setParameter('val', $companyId)
                ->orderBy('patient.id', 'ASC')
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
     * @return Patient[] Returns an array of Patient objects
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findDataTable($start, $length,$search,$orders,$columns,$companyId)
    {

        $query = $this->createQueryBuilder('patient');
        $countQuery = $this->createQueryBuilder('patient');

        $countQuery->select('COUNT(patient)');

            $query->select('patient.id')
                ->addSelect('patient.firstName')
                ->addSelect('patient.lastName')
                ->addSelect('patient.gender')
                ->addSelect('patient.birthdate')
                ->addSelect('patient.email')
                ->addSelect('patient.phone')
                ->where('patient.company = :val');

        $searchQuery = null;

        if ($search['value'] != '') {


            if(is_numeric($search['value'])){
                $searchItem = $search['value'];

                $searchQuery = 'patient.id LIKE \'%' . $searchItem . '%\'';

            }elseif(!is_numeric($search['value'])){

                $searchItem = $search['value'];

                $searchQuery = 'patient.firstName LIKE \'%' . $searchItem . '%\' OR  patient.lastName LIKE \'%' . $searchItem .'%\'';

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
            ->orderBy('patient.id', 'ASC');

        $results = $query->getQuery()->getArrayResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();

        return array(
            "results" 		=> $results,
            "countResult"	=> $countResult
        );


    }

    /**
     * @param $companyId
     * @return Patient[] Returns an array of Patient objects
     */
    public function findArrayByCompany($companyId)
    {
        return $this->createQueryBuilder('patient')
            ->where('patient.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('patient.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * @param $companyId
     * @return Patient[] Returns an array of Patient objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('patient')
            ->andWhere('patient.company = :val')
            ->setParameter('val', $companyId)
            ->orderBy('patient.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $companyId
     * @param $id
     * @return Patient
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('patient')
            ->andWhere('patient.company = :company')
            ->andWhere('patient.id = :id')
            ->setParameter('company', $companyId)
            ->setParameter('id', $id)
            ->orderBy('patient.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
