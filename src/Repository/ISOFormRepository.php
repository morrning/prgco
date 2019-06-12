<?php

namespace App\Repository;

use App\Entity\ISOForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ISOForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method ISOForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method ISOForm[]    findAll()
 * @method ISOForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ISOFormRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ISOForm::class);
    }

    // /**
    //  * @return ISOForm[] Returns an array of ISOForm objects
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
    public function findOneBySomeField($value): ?ISOForm
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
