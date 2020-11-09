<?php

namespace App\Repository;

use App\Entity\CMPassengerDocType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CMPassengerDocType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMPassengerDocType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMPassengerDocType[]    findAll()
 * @method CMPassengerDocType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMPassengerDocTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CMPassengerDocType::class);
    }

    // /**
    //  * @return CMPassengerDocType[] Returns an array of CMPassengerDocType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CMPassengerDocType
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
