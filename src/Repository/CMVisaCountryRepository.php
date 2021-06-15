<?php

namespace App\Repository;

use App\Entity\CMVisaCountry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMVisaCountry|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMVisaCountry|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMVisaCountry[]    findAll()
 * @method CMVisaCountry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMVisaCountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMVisaCountry::class);
    }

    // /**
    //  * @return CMVisaCountry[] Returns an array of CMVisaCountry objects
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
    public function findOneBySomeField($value): ?CMVisaCountry
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
