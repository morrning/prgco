<?php

namespace App\Repository;

use App\Entity\ICTDeviceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ICTDeviceType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTDeviceType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTDeviceType[]    findAll()
 * @method ICTDeviceType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTDeviceTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ICTDeviceType::class);
    }

    // /**
    //  * @return ICTDeviceType[] Returns an array of ICTDeviceType objects
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
    public function findOneBySomeField($value): ?ICTDeviceType
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
