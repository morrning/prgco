<?php

namespace App\Repository;

use App\Entity\CMVisaSendWay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CMVisaSendWay|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMVisaSendWay|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMVisaSendWay[]    findAll()
 * @method CMVisaSendWay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMVisaSendWayRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CMVisaSendWay::class);
    }

    // /**
    //  * @return CMVisaSendWay[] Returns an array of CMVisaSendWay objects
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
    public function findOneBySomeField($value): ?CMVisaSendWay
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
