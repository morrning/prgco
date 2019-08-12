<?php


namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity;

class ACC
{

    protected $em;

    function __construct(EntityMGR $entityMgr)
    {
        $this->em = $entityMgr;
    }


    public function addAccount($label,$user=null,$position=null){
        if(!$this->hasAccount($user,$position)){
            $acc = new Entity\ACCaccount();
            $acc->setLabel($label);
            $acc->setPosition($position);
            $acc->setUser($user);
            return $this->em->insertEntity($acc);
        }
    }

    public function hasAccount($user=null,$position=null){
        $exist=false;
        if(!is_null($this->em->findOneBy('App:ACCaccount',['user'=>$user])))
            $exist = true;
        elseif (!is_null($this->em->findOneBy('App:ACCaccount',['position'=>$position])))
            $exist = true;
        return $exist;
    }

    public function addDocument($title,$submitter,$iccenterCode,$icUser){
        $doc = new Entity\ACCdoc();
        $doc->setSubmitter($submitter);
        $doc->setDateSubmit(time());
        $doc->setDocTitle($title);
        $doc->setIccenter($iccenterCode);
        $this->em->insertEntity($doc);
        return $doc;
    }

    public function addDocumentItem($document,$moneyType,$moneyValue)
    {
        $item = new Entity\ACCdocItem();
        $item->setDoc($document);
        $item->setMoneyValue($moneyValue);
        $item->setMoneyType($moneyType);
        return $this->em->insertEntity($item);
    }

    public function getICCenter($code){
        return $this->em->findOneBy('App:ACCiccenter',['iccode'=>$code]);
    }
}