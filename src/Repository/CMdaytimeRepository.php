<?php

namespace App\Repository;

use App\Entity\CMdaytime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CMdaytime|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMdaytime|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMdaytime[]    findAll()
 * @method CMdaytime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMdaytimeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CMdaytime::class);
    }

    // /**
    //  * @return CMdaytime[] Returns an array of CMdaytime objects
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
    public function findOneBySomeField($value): ?CMdaytime
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
