<?php

namespace App\Repository;

use App\Entity\ICTDoing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ICTDoing|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTDoing|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTDoing[]    findAll()
 * @method ICTDoing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTDoingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ICTDoing::class);
    }

    // /**
    //  * @return ICTDoing[] Returns an array of ICTDoing objects
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
    public function findOneBySomeField($value): ?ICTDoing
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
