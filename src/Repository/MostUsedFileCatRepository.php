<?php

namespace App\Repository;

use App\Entity\MostUsedFileCat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MostUsedFileCat|null find($id, $lockMode = null, $lockVersion = null)
 * @method MostUsedFileCat|null findOneBy(array $criteria, array $orderBy = null)
 * @method MostUsedFileCat[]    findAll()
 * @method MostUsedFileCat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MostUsedFileCatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MostUsedFileCat::class);
    }

    // /**
    //  * @return MostUsedFileCat[] Returns an array of MostUsedFileCat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MostUsedFileCat
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
