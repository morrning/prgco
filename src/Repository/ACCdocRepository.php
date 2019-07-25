<?php

namespace App\Repository;

use App\Entity\ACCdoc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ACCdoc|null find($id, $lockMode = null, $lockVersion = null)
 * @method ACCdoc|null findOneBy(array $criteria, array $orderBy = null)
 * @method ACCdoc[]    findAll()
 * @method ACCdoc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ACCdocRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ACCdoc::class);
    }

    // /**
    //  * @return ACCdoc[] Returns an array of ACCdoc objects
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
    public function findOneBySomeField($value): ?ACCdoc
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
