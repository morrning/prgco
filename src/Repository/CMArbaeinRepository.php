<?php

namespace App\Repository;

use App\Entity\CMArbaein;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CMArbaein|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMArbaein|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMArbaein[]    findAll()
 * @method CMArbaein[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMArbaeinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMArbaein::class);
    }

    // /**
    //  * @return CMArbaein[] Returns an array of CMArbaein objects
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
    public function findOneBySomeField($value): ?CMArbaein
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
