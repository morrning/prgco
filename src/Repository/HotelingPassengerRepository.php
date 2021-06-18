<?php

namespace App\Repository;

use App\Entity\HotelingPassenger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HotelingPassenger|null find($id, $lockMode = null, $lockVersion = null)
 * @method HotelingPassenger|null findOneBy(array $criteria, array $orderBy = null)
 * @method HotelingPassenger[]    findAll()
 * @method HotelingPassenger[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotelingPassengerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HotelingPassenger::class);
    }

    // /**
    //  * @return HotelingPassenger[] Returns an array of HotelingPassenger objects
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
    public function findOneBySomeField($value): ?HotelingPassenger
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
