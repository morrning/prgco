<?php

namespace App\Repository;

use App\Entity\HssePenalty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HssePenalty|null find($id, $lockMode = null, $lockVersion = null)
 * @method HssePenalty|null findOneBy(array $criteria, array $orderBy = null)
 * @method HssePenalty[]    findAll()
 * @method HssePenalty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HssePenaltyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HssePenalty::class);
    }

    // /**
    //  * @return HssePenalty[] Returns an array of HssePenalty objects
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
    public function findOneBySomeField($value): ?HssePenalty
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
