<?php

namespace App\Repository;

use App\Entity\CMPassengerPersonalDoc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMPassengerPersonalDoc|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMPassengerPersonalDoc|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMPassengerPersonalDoc[]    findAll()
 * @method CMPassengerPersonalDoc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMPassengerPersonalDocRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMPassengerPersonalDoc::class);
    }

    // /**
    //  * @return CMPassengerPersonalDoc[] Returns an array of CMPassengerPersonalDoc objects
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
    public function findOneBySomeField($value): ?CMPassengerPersonalDoc
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
