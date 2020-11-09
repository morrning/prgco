<?php

namespace App\Repository;

use App\Entity\SysScript;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SysScript|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysScript|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysScript[]    findAll()
 * @method SysScript[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysScriptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SysScript::class);
    }

    // /**
    //  * @return SysScript[] Returns an array of SysScript objects
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
    public function findOneBySomeField($value): ?SysScript
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
