<?php

namespace App\Repository;

use App\Entity\ICTRequestState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ICTRequestState|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTRequestState|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTRequestState[]    findAll()
 * @method ICTRequestState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTRequestStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ICTRequestState::class);
    }

    // /**
    //  * @return ICTRequestState[] Returns an array of ICTRequestState objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ICTRequestState
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
