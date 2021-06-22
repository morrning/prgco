<?php

namespace App\Repository;

use App\Entity\CMVisaReq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMVisaReq|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMVisaReq|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMVisaReq[]    findAll()
 * @method CMVisaReq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMVisaReqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMVisaReq::class);
    }

    // /**
    //  * @return CMVisaReq[] Returns an array of CMVisaReq objects
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
    public function findOneBySomeField($value): ?CMVisaReq
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
