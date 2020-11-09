<?php

namespace App\Repository;

use App\Entity\HotelingRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HotelingRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method HotelingRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method HotelingRoom[]    findAll()
 * @method HotelingRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotelingRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HotelingRoom::class);
    }

    // /**
    //  * @return HotelingRoom[] Returns an array of HotelingRoom objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HotelingRoom
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
