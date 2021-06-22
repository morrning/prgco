<?php

namespace App\Repository;

use App\Entity\CMVisaState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CMVisaState|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMVisaState|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMVisaState[]    findAll()
 * @method CMVisaState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMVisaStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMVisaState::class);
    }

    // /**
    //  * @return CMVisaState[] Returns an array of CMVisaState objects
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
    public function findOneBySomeField($value): ?CMVisaState
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
