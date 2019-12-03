<?php

namespace App\Repository;

use App\Entity\PhpConsoleDoctrineSchemaUpdateForce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PhpConsoleDoctrineSchemaUpdateForce|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhpConsoleDoctrineSchemaUpdateForce|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhpConsoleDoctrineSchemaUpdateForce[]    findAll()
 * @method PhpConsoleDoctrineSchemaUpdateForce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhpConsoleDoctrineSchemaUpdateForceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhpConsoleDoctrineSchemaUpdateForce::class);
    }

    // /**
    //  * @return PhpConsoleDoctrineSchemaUpdateForce[] Returns an array of PhpConsoleDoctrineSchemaUpdateForce objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PhpConsoleDoctrineSchemaUpdateForce
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
