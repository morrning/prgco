<?php

namespace App\Repository;

use App\Entity\CMCities;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMCities|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMCities|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMCities[]    findAll()
 * @method CMCities[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMCitiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMCities::class);
    }

    // /**
    //  * @return CMCities[] Returns an array of CMCities objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CMCities
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
