<?php

namespace App\Repository;

use App\Entity\ACCiccenter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ACCiccenter|null find($id, $lockMode = null, $lockVersion = null)
 * @method ACCiccenter|null findOneBy(array $criteria, array $orderBy = null)
 * @method ACCiccenter[]    findAll()
 * @method ACCiccenter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ACCiccenterRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ACCiccenter::class);
    }

    // /**
    //  * @return ACCiccenter[] Returns an array of ACCiccenter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ACCiccenter
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
