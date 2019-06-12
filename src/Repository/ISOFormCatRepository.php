<?php

namespace App\Repository;

use App\Entity\ISOFormCat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ISOFormCat|null find($id, $lockMode = null, $lockVersion = null)
 * @method ISOFormCat|null findOneBy(array $criteria, array $orderBy = null)
 * @method ISOFormCat[]    findAll()
 * @method ISOFormCat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ISOFormCatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ISOFormCat::class);
    }

    // /**
    //  * @return ISOFormCat[] Returns an array of ISOFormCat objects
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
    public function findOneBySomeField($value): ?ISOFormCat
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
