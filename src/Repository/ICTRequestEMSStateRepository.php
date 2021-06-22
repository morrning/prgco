<?php

namespace App\Repository;

use App\Entity\ICTRequestEMSState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ICTRequestEMSState|null find($id, $lockMode = null, $lockVersion = null)
 * @method ICTRequestEMSState|null findOneBy(array $criteria, array $orderBy = null)
 * @method ICTRequestEMSState[]    findAll()
 * @method ICTRequestEMSState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ICTRequestEMSStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ICTRequestEMSState::class);
    }

    // /**
    //  * @return ICTRequestEMSState[] Returns an array of ICTRequestEMSState objects
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
    public function findOneBySomeField($value): ?ICTRequestEMSState
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
