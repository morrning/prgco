<?php

namespace App\Repository;

use App\Entity\CMPassenger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMPassenger|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMPassenger|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMPassenger[]    findAll()
 * @method CMPassenger[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMPassengerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMPassenger::class);
    }

    /**
     * @return CMPassenger[] Returns an array of CMPassenger objects
    */
    public function getNearPassportExpire()
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


    /*
    public function findOneBySomeField($value): ?CMPassenger
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
