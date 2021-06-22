<?php

namespace App\Repository;

use App\Entity\ICTRequestType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ICTRequestType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTRequestType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTRequestType[]    findAll()
 * @method ICTRequestType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTRequestTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ICTRequestType::class);
    }

    // /**
    //  * @return ICTRequestType[] Returns an array of ICTRequestType objects
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
    public function findOneBySomeField($value): ?ICTRequestType
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
