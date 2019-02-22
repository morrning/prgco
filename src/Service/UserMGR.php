<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity;


class UserMGR
{

    protected $em;
    protected $session;
    protected $user;

    function __construct(EntityMGR $entityMgr)
    {
        $this->em = $entityMgr;
        $this->session = new Session();
        $this->user = null;
    }

    public function currentUser(){
        if(!is_null($this->user))
            return $this->user;
        $sessionID = $this->session->getId();
        $this->user = $this->em->findOneBy('App:SysUser',['uniqueID'=>$sessionID]);
        return $this->user;
    }

    public function login($username, $password){

        $params = ["username"=>$username,"password"=>md5($password)];
        $user = $this->em->findOneBy('App:SysUser',$params);
        if(! is_null($user))
        {
            $user->setUniqueID($this->session->getId());
            $this->em->update($user);
            return true;
        }
        return false;
    }

    public function logout(){
        if($this->isLogedIn()){
            $user = $this->currentUser();
            $user->setUniqueID('');
            return $this->em->update($user);
        }
    }

    public function isLogedIn()
    {
        $result = $this->currentUser();
        if(! is_null($result))
            if($result->getUniqueID() != '')
                return true;
        return false;
    }

    //------------- POSITION OPERATIONS ---------------

    public function currentPosition()
    {
        if($this->isLogedIn()){
            return $this->em->findOneBy('App:SysPosition',['userID'=>$this->currentUser()->getId(),'isDefault'=>1]);
        }
    }

    public function currentUserPositions()
    {
        if($this->isLogedIn()){
            return $this->em->findBy('App:SysPosition',['userID'=>$this->currentUser()->getId()]);
        }
    }

    public function changeDefaultPosition($PID,$UID=null)
    {
        if(is_null($UID)) $UID = $this->currentUser()->getId();

        $orm = $this->em->getORM();
        $orm->createQueryBuilder()->update('App:SysPosition')->set('isDefault',0)->getQuery()->execute();
        $position = $this->em->find('App:SysPosition',$PID);
        $position->setIsDefault(1);
        $this->em->update($position);
        return true;
    }

    //--------------- PERMISSION CONTROLL ---------------
    public function hasPermission($permissionName,$bundle='CORE',$option=null)
    {
        if($this->isLogedIn()){
            $groups = explode(',',$this->currentUser()->getGroups());
            $params = [
                'groupName'=>$permissionName,
                'options'=>$option,
                'bundle'=>$bundle
            ];
            $grp = $this->em->findOneBy('App:SysGroup',$params);
            if(array_search($grp->getId(),$groups) !== false){
                return true;
            }

        }
        return false;
    }

}