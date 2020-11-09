<?php

namespace App\Repository;

use App\Entity\SysMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SysMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysMessage[]    findAll()
 * @method SysMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SysMessage::class);
    }

    // /**
    //  * @return SysMessage[] Returns an array of SysMessage objects
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
    public function findOneBySomeField($value): ?SysMessage
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
