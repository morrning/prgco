<?php

namespace App\Repository;

use App\Entity\SysMenuItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SysMenuItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysMenuItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysMenuItem[]    findAll()
 * @method SysMenuItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysMenuItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SysMenuItem::class);
    }

    // /**
    //  * @return SysMenuItem[] Returns an array of SysMenuItem objects
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
    public function findOneBySomeField($value): ?SysMenuItem
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
