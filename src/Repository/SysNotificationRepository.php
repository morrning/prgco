<?php

namespace App\Repository;

use App\Entity\SysNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SysNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysNotification[]    findAll()
 * @method SysNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysNotificationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SysNotification::class);
    }

    // /**
    //  * @return SysNotification[] Returns an array of SysNotification objects
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
    public function findOneBySomeField($value): ?SysNotification
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
