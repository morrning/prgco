<?php

namespace App\Repository;

use App\Entity\CMPassengerType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMPassengerType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMPassengerType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMPassengerType[]    findAll()
 * @method CMPassengerType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMPassengerTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMPassengerType::class);
    }

    // /**
    //  * @return CMPassengerType[] Returns an array of CMPassengerType objects
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
    public function findOneBySomeField($value): ?CMPassengerType
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
