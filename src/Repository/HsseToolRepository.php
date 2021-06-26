<?php

namespace App\Repository;

use App\Entity\HsseTool;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HsseTool|null find($id, $lockMode = null, $lockVersion = null)
 * @method HsseTool|null findOneBy(array $criteria, array $orderBy = null)
 * @method HsseTool[]    findAll()
 * @method HsseTool[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HsseToolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HsseTool::class);
    }

    // /**
    //  * @return HsseTool[] Returns an array of HsseTool objects
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
    public function findOneBySomeField($value): ?HsseTool
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
