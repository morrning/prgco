<?php

namespace App\Repository;

use App\Entity\SysPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SysPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysPosition[]    findAll()
 * @method SysPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysPositionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SysPosition::class);
    }

    // /**
    //  * @return SysPosition[] Returns an array of SysPosition objects
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
    public function findOneBySomeField($value): ?SysPosition
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
