<?php

namespace App\Repository;

use App\Entity\ICTRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ICTRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTRequest[]    findAll()
 * @method ICTRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTRequestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ICTRequest::class);
    }

    // /**
    //  * @return ICTRequest[] Returns an array of ICTRequest objects
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
    public function findOneBySomeField($value): ?ICTRequest
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
