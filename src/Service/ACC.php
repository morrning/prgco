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

    /**
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 32) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    public function addAccount($label,$user=null){
        $acc = new Entity\ACCaccount();
        $acc->setLabel($label);
        $acc->setUser($user);
        $acc->setAccountNo($this->RandomString(32));
        $this->em->insertEntity($acc);
        return $acc;
    }

    public function hasAccount($user=null,$acountNo=null){
        $exist=false;
        if(!is_null($this->em->findOneBy('App:ACCaccount',['user'=>$user])))
            $exist = true;
        elseif (!is_null($this->em->findOneBy('App:ACCaccount',['accountNo'=>$acountNo])))
            $exist = true;
        return $exist;
    }

    public function getAccountByAccountNo($acountNo){
        return $this->em->findOneBy('App:ACCaccount',['accountNo'=>$acountNo]);
    }

    public function getAccountByUser($user){
        return $this->em->findOneBy('App:ACCaccount',['user'=>$user]);
    }

    public function addDocument($title,$moneyType,$submitter,$iccenterCode,$account){
        $doc = new Entity\ACCdoc();
        $doc->setSubmitter($submitter);
        $doc->setDateSubmit(time());
        $doc->setDocTitle($title);
        $doc->setMoney($moneyType);
        $doc->setIccenter($iccenterCode);
        $doc->setAccount($account);
        $this->em->insertEntity($doc);
        return $doc;
    }

    public function addDocumentItem($document,$moneyValue)
    {
        $item = new Entity\ACCdocItem();
        $item->setDoc($document);
        $item->setMoneyValue($moneyValue);
        $this->em->insertEntity($item);

        $document->setTotalValue($document->getTotalValue() + $moneyValue);
        return $this->em->update($document);
    }

    public function getICCenter($code){
        return $this->em->findOneBy('App:ACCiccenter',['iccode'=>$code]);
    }
}