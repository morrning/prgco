<?php

namespace App\Repository;

use App\Entity\ISOFormType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ISOFormType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ISOFormType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ISOFormType[]    findAll()
 * @method ISOFormType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ISOFormTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ISOFormType::class);
    }

    // /**
    //  * @return ISOFormType[] Returns an array of ISOFormType objects
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
    public function findOneBySomeField($value): ?ISOFormType
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
