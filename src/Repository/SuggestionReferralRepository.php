<?php

namespace App\Repository;

use App\Entity\SuggestionReferral;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SuggestionReferral|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuggestionReferral|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuggestionReferral[]    findAll()
 * @method SuggestionReferral[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuggestionReferralRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SuggestionReferral::class);
    }

    // /**
    //  * @return SuggestionReferral[] Returns an array of SuggestionReferral objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SuggestionReferral
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
