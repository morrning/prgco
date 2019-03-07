<?php

namespace App\Repository;

use App\Entity\SysGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SysGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysGroup[]    findAll()
 * @method SysGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SysGroup::class);
    }

    // /**
    //  * @return SysGroup[] Returns an array of SysGroup objects
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
    public function findOneBySomeField($value): ?SysGroup
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
