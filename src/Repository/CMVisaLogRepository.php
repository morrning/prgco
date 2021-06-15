<?php

namespace App\Repository;

use App\Entity\CMVisaLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMVisaLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMVisaLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMVisaLog[]    findAll()
 * @method CMVisaLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMVisaLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMVisaLog::class);
    }

    // /**
    //  * @return CMVisaLog[] Returns an array of CMVisaLog objects
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
    public function findOneBySomeField($value): ?CMVisaLog
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
