<?php

namespace App\Repository;

use App\Entity\ICTMachineType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ICTMachineType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTMachineType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTMachineType[]    findAll()
 * @method ICTMachineType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTMachineTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ICTMachineType::class);
    }

    // /**
    //  * @return ICTMachineType[] Returns an array of ICTMachineType objects
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
    public function findOneBySomeField($value): ?ICTMachineType
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
