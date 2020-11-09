<?php

namespace App\Repository;

use App\Entity\ACCMoney;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ACCMoney|null find($id, $lockMode = null, $lockVersion = null)
 * @method ACCMoney|null findOneBy(array $criteria, array $orderBy = null)
 * @method ACCMoney[]    findAll()
 * @method ACCMoney[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ACCMoneyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ACCMoney::class);
    }

    // /**
    //  * @return ACCMoney[] Returns an array of ACCMoney objects
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
    public function findOneBySomeField($value): ?ACCMoney
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
