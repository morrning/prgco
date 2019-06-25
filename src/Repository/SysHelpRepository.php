<?php

namespace App\Repository;

use App\Entity\SysHelp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SysHelp|null find($id, $lockMode = null, $lockVersion = null)
 * @method SysHelp|null findOneBy(array $criteria, array $orderBy = null)
 * @method SysHelp[]    findAll()
 * @method SysHelp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SysHelpRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SysHelp::class);
    }

    // /**
    //  * @return SysHelp[] Returns an array of SysHelp objects
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
    public function findOneBySomeField($value): ?SysHelp
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
