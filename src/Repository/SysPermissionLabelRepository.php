<?php

namespace App\Repository;

use App\Entity\SysPermissionLabel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SysPermissionLabel|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysPermissionLabel|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysPermissionLabel[]    findAll()
 * @method SysPermissionLabel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysPermissionLabelRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SysPermissionLabel::class);
    }

    // /**
    //  * @return SysPermissionLabel[] Returns an array of SysPermissionLabel objects
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
    public function findOneBySomeField($value): ?SysPermissionLabel
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
