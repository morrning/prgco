<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity as Entity;

class LogMGR
{
    protected $em;
    protected $userMGR;
    function __construct(EntityMGR $entityMgr,UserMGR $userMGR)
    {
        $this->em = $entityMgr;
        $this->userMGR = $userMGR;
    }

    public function addEvent($SID,$operation,$des,$bundle='CORE',$ip){
        $log = new Entity\SysLog();
        $log->setOperation($operation);
        $log->setDes($des);
        $log->setSid($SID);
        $log->setIpSource($ip);
        $log->setBundleLabel($bundle);
        $log->setUsername($this->userMGR->currentUser()->getUsername());
        $log->setFullName($this->userMGR->currentPosition()->getPublicLabel());
        $log->setDateSubmit(time());
        $this->em->insertEntity($log);
    }

    public function getEvents($bundle,$sid){
        return $this->em->findBy('App:SysLog',['bundleLabel'=>$bundle,'sid'=>$sid]);
    }
}