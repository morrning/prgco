<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;


class EntityMGR
{
    private $em;

    function __construct(EntityManagerInterface  $entityManager)
    {
        $this->em = $entityManager;
    }

    //  DELETE RECORD
    //-----------------------------------------
    public function remove($entityName,$id)
    {
        $res = $this->em->getRepository($entityName)->find($id);
        $this->em->remove($res);
        $this->em->flush();
    }

    // SELECT ENTITY
    //-------------------------------------------
    public function find($entityName,$id)
    {
        return $this->em->getRepository($entityName)->find($id);
    }

    public function findBy($entity,$params = [],$orders =[])
    {
        return $this->em->getRepository($entity)->findBy($params,$orders);
    }

    public function findOneBy($entity,$params = [])
    {
        return  $this->em->getRepository($entity)->findOneBy($params);
    }

    public function findByPage($entityName,$page=1,$perPage=20)
    {
        return $this->em->createQueryBuilder('q')
            ->select('q')
            ->from($entityName,'q')
            ->setMaxResults($perPage)
            ->setFirstResult($perPage * ($page -1) )
            ->orderBy('q.id','DESC')
            ->getQuery()
            ->execute();
    }

    //  INSERT/UPDATE ENTITY TO DATABASE
    //------------------------------------------------
    public function insertEntity($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity->getId();
    }

    public function update($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    // ENTITY MANAGER OBJ
    //------------------------------------------------
    public function getORM()
    {
        $ORM = clone $this->em;
        return $ORM;
    }
}