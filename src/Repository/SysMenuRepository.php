<?php

namespace App\Repository;

use App\Entity\SysMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SysMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysMenu[]    findAll()
 * @method SysMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysMenuRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SysMenu::class);
    }

    // /**
    //  * @return SysMenu[] Returns an array of SysMenu objects
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
    public function findOneBySomeField($value): ?SysMenu
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
