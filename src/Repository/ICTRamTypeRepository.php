<?php

namespace App\Repository;

use App\Entity\ICTRamType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ICTRamType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTRamType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTRamType[]    findAll()
 * @method ICTRamType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTRamTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ICTRamType::class);
    }

    // /**
    //  * @return ICTRamType[] Returns an array of ICTRamType objects
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
    public function findOneBySomeField($value): ?ICTRamType
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
