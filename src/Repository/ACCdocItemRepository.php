<?php

namespace App\Repository;

use App\Entity\ACCdocItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ACCdocItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ACCdocItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ACCdocItem[]    findAll()
 * @method ACCdocItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ACCdocItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ACCdocItem::class);
    }

    // /**
    //  * @return ACCdocItem[] Returns an array of ACCdocItem objects
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
    public function findOneBySomeField($value): ?ACCdocItem
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
