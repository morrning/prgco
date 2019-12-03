<?php

namespace App\Repository;

use App\Entity\CMListUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CMListUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMListUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMListUser[]    findAll()
 * @method CMListUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMListUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMListUser::class);
    }

    // /**
    //  * @return CMListUser[] Returns an array of CMListUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CMListUser
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
