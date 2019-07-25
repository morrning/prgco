<?php

namespace App\Repository;

use App\Entity\ACCicname;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ACCicname|null find($id, $lockMode = null, $lockVersion = null)
 * @method ACCicname|null findOneBy(array $criteria, array $orderBy = null)
 * @method ACCicname[]    findAll()
 * @method ACCicname[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ACCicnameRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ACCicname::class);
    }

    // /**
    //  * @return ACCicname[] Returns an array of ACCicname objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ACCicname
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
