<?php

namespace App\Repository;

use App\Entity\CMacceptIF;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMacceptIF|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMacceptIF|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMacceptIF[]    findAll()
 * @method CMacceptIF[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMacceptIFRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMacceptIF::class);
    }

    // /**
    //  * @return CMacceptIF[] Returns an array of CMacceptIF objects
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
    public function findOneBySomeField($value): ?CMacceptIF
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
