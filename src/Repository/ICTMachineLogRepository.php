<?php

namespace App\Repository;

use App\Entity\ICTMachineLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ICTMachineLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTMachineLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTMachineLog[]    findAll()
 * @method ICTMachineLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTMachineLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ICTMachineLog::class);
    }

    // /**
    //  * @return ICTMachineLog[] Returns an array of ICTMachineLog objects
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
    public function findOneBySomeField($value): ?ICTMachineLog
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
