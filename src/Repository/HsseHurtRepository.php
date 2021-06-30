<?php

namespace App\Repository;

use App\Entity\HsseHurt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HsseHurt|null find($id, $lockMode = null, $lockVersion = null)
 * @method HsseHurt|null findOneBy(array $criteria, array $orderBy = null)
 * @method HsseHurt[]    findAll()
 * @method HsseHurt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HsseHurtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HsseHurt::class);
    }

    // /**
    //  * @return HsseHurt[] Returns an array of HsseHurt objects
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
    public function findOneBySomeField($value): ?HsseHurt
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
