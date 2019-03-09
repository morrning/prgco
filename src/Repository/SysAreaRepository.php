<?php

namespace App\Repository;

use App\Entity\SysArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SysArea|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysArea|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysArea[]    findAll()
 * @method SysArea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysAreaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SysArea::class);
    }

    // /**
    //  * @return SysArea[] Returns an array of SysArea objects
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
    public function findOneBySomeField($value): ?SysArea
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
