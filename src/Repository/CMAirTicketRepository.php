<?php

namespace App\Repository;

use App\Entity\CMAirTicket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CMAirTicket|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMAirTicket|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMAirTicket[]    findAll()
 * @method CMAirTicket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMAirTicketRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CMAirTicket::class);
    }

    // /**
    //  * @return CMAirTicket[] Returns an array of CMAirTicket objects
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
    public function findOneBySomeField($value): ?CMAirTicket
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
