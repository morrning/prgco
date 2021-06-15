<?php

namespace App\Repository;

use App\Entity\SysConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SysConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysConfig[]    findAll()
 * @method SysConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SysConfig::class);
    }

    // /**
    //  * @return SysConfig[] Returns an array of SysConfig objects
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
    public function findOneBySomeField($value): ?SysConfig
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
