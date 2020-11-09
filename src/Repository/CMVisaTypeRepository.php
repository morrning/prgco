<?php

namespace App\Repository;

use App\Entity\CMVisaType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CMVisaType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CMVisaType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CMVisaType[]    findAll()
 * @method CMVisaType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CMVisaTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CMVisaType::class);
    }

    // /**
    //  * @return CMVisaType[] Returns an array of CMVisaType objects
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
    public function findOneBySomeField($value): ?CMVisaType
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
