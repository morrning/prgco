<?php

namespace App\Repository;

use App\Entity\HsseGuid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HsseGuid|null find($id, $lockMode = null, $lockVersion = null)
 * @method HsseGuid|null findOneBy(array $criteria, array $orderBy = null)
 * @method HsseGuid[]    findAll()
 * @method HsseGuid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HsseGuidRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HsseGuid::class);
    }

    // /**
    //  * @return HsseGuid[] Returns an array of HsseGuid objects
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
    public function findOneBySomeField($value): ?HsseGuid
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
