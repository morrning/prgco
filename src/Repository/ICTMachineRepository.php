<?php

namespace App\Repository;

use App\Entity\ICTMachine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ICTMachine|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTMachine|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTMachine[]    findAll()
 * @method ICTMachine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTMachineRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ICTMachine::class);
    }

    // /**
    //  * @return ICTMachine[] Returns an array of ICTMachine objects
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
    public function findOneBySomeField($value): ?ICTMachine
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
