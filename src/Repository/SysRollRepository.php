<?php

namespace App\Repository;

use App\Entity\SysRoll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SysRoll|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysRoll|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysRoll[]    findAll()
 * @method SysRoll[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysRollRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SysRoll::class);
    }

    // /**
    //  * @return SysRoll[] Returns an array of SysRoll objects
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
    public function findOneBySomeField($value): ?SysRoll
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
