<?php

namespace App\Repository;

use App\Entity\ACCaccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ACCaccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method ACCaccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method ACCaccount[]    findAll()
 * @method ACCaccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ACCaccountRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ACCaccount::class);
    }

    // /**
    //  * @return ACCaccount[] Returns an array of ACCaccount objects
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
    public function findOneBySomeField($value): ?ACCaccount
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
