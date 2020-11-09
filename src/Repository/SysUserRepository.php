<?php

namespace App\Repository;

use App\Entity\SysUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SysUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysUser[]    findAll()
 * @method SysUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SysUser::class);
    }

    // /**
    //  * @return SysUser[] Returns an array of SysUser objects
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
    public function findOneBySomeField($value): ?SysUser
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
