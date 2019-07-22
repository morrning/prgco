<?php

namespace App\Repository;

use App\Entity\CMAirTicketState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CMAirTicketState|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMAirTicketState|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMAirTicketState[]    findAll()
 * @method CMAirTicketState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMAirTicketStateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CMAirTicketState::class);
    }

    // /**
    //  * @return CMAirTicketState[] Returns an array of CMAirTicketState objects
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
    public function findOneBySomeField($value): ?CMAirTicketState
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
