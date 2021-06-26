<?php

namespace App\Repository;

use App\Entity\HsseHealth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HsseHealth|null find($id, $lockMode = null, $lockVersion = null)
 * @method HsseHealth|null findOneBy(array $criteria, array $orderBy = null)
 * @method HsseHealth[]    findAll()
 * @method HsseHealth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HsseHealthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HsseHealth::class);
    }

    // /**
    //  * @return HsseHealth[] Returns an array of HsseHealth objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HsseHealth
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
