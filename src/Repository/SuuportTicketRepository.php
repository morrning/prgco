<?php

namespace App\Repository;

use App\Entity\SuuportTicket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SuuportTicket|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuuportTicket|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuuportTicket[]    findAll()
 * @method SuuportTicket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuuportTicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuuportTicket::class);
    }

    // /**
    //  * @return SuuportTicket[] Returns an array of SuuportTicket objects
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
    public function findOneBySomeField($value): ?SuuportTicket
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
