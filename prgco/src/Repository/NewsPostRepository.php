<?php

namespace App\Repository;

use App\Entity\NewsPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NewsPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsPost[]    findAll()
 * @method NewsPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsPostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NewsPost::class);
    }

    // /**
    //  * @return NewsPost[] Returns an array of NewsPost objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NewsPost
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
