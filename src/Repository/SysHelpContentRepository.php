<?php

namespace App\Repository;

use App\Entity\SysHelpContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SysHelpContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysHelpContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysHelpContent[]    findAll()
 * @method SysHelpContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysHelpContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SysHelpContent::class);
    }

    // /**
    //  * @return SysHelpContent[] Returns an array of SysHelpContent objects
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
    public function findOneBySomeField($value): ?SysHelpContent
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
