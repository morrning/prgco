<?php

namespace App\Repository;

use App\Entity\ICTCpuType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ICTCpuType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTCpuType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTCpuType[]    findAll()
 * @method ICTCpuType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTCpuTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ICTCpuType::class);
    }

    // /**
    //  * @return ICTCpuType[] Returns an array of ICTCpuType objects
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
    public function findOneBySomeField($value): ?ICTCpuType
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
