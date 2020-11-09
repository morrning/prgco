<?php

namespace App\Repository;

use App\Entity\StaticMonth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StaticMonth|null find($id, $lockMode = null, $lockVersion = null)
 * @method StaticMonth|null findOneBy(array $criteria, array $orderBy = null)
 * @method StaticMonth[]    findAll()
 * @method StaticMonth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaticMonthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaticMonth::class);
    }

    // /**
    //  * @return StaticMonth[] Returns an array of StaticMonth objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StaticMonth
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
