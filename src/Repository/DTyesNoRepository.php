<?php

namespace App\Repository;

use App\Entity\DTyesNo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DTyesNo|null find($id, $lockMode = null, $lockVersion = null)
 * @method DTyesNo|null findOneBy(array $criteria, array $orderBy = null)
 * @method DTyesNo[]    findAll()
 * @method DTyesNo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DTyesNoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DTyesNo::class);
    }

    // /**
    //  * @return DTyesNo[] Returns an array of DTyesNo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DTyesNo
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
