<?php

namespace App\Repository;

use App\Entity\CMList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMList|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMList|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMList[]    findAll()
 * @method CMList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMList::class);
    }

    // /**
    //  * @return CMList[] Returns an array of CMList objects
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
    public function findOneBySomeField($value): ?CMList
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
