<?php

namespace App\Repository;

use App\Entity\CMAirway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMAirway|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMAirway|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMAirway[]    findAll()
 * @method CMAirway[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMAirwayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMAirway::class);
    }

    // /**
    //  * @return CMAirway[] Returns an array of CMAirway objects
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
    public function findOneBySomeField($value): ?CMAirway
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
