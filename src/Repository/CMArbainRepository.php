<?php

namespace App\Repository;

use App\Entity\CMArbain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CMArbain|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMArbain|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMArbain[]    findAll()
 * @method CMArbain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMArbainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMArbain::class);
    }

    // /**
    //  * @return CMArbain[] Returns an array of CMArbain objects
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
    public function findOneBySomeField($value): ?CMArbain
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
