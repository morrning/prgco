<?php

namespace App\Repository;

use App\Entity\HRMemploye;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HRMemploye|null find($id, $lockMode = null, $lockVersion = null)
 * @method HRMemploye|null findOneBy(array $criteria, array $orderBy = null)
 * @method HRMemploye[]    findAll()
 * @method HRMemploye[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HRMemployeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HRMemploye::class);
    }

    // /**
    //  * @return HRMemploye[] Returns an array of HRMemploye objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HRMemploye
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
