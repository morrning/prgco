<?php

namespace App\Repository;

use App\Entity\HotelingRoomWCType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HotelingRoomWCType|null find($id, $lockMode = null, $lockVersion = null)
 * @method HotelingRoomWCType|null findOneBy(array $criteria, array $orderBy = null)
 * @method HotelingRoomWCType[]    findAll()
 * @method HotelingRoomWCType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotelingRoomWCTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HotelingRoomWCType::class);
    }

    // /**
    //  * @return HotelingRoomWCType[] Returns an array of HotelingRoomWCType objects
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
    public function findOneBySomeField($value): ?HotelingRoomWCType
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
