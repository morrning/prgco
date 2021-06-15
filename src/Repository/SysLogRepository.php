<?php

namespace App\Repository;

use App\Entity\SysLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SysLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysLog[]    findAll()
 * @method SysLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SysLog::class);
    }

    // /**
    //  * @return SysLog[] Returns an array of SysLog objects
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
    public function findOneBySomeField($value): ?SysLog
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
