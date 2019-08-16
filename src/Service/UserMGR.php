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
    public function hasPermission($permissionName,$bundle='CORE',$option=null,$pid = 1)
    {
        if($permissionName != 'superAdmin'){
            if($this->hasPermission('superAdmin')){
                return true;
            }
        }
        if($this->isLogedIn()){
            $groups = explode(',',$this->currentPosition()->getGroups());
            $params = [
                'groupName'=>$permissionName,
                'options'=>$option,
                'bundle'=>$bundle,
                'PID'=>$pid
            ];
            $grp = $this->em->findOneBy('App:SysGroup',$params);
            if(is_null($grp))
            {
                $grp = new Entity\SysGroup();
                $grp->setBundle($bundle);
                if(! is_null($option))
                    $grp->setOptions($option);
                if(! is_null($pid))
                    $grp->setPID($pid);
                $grp->setGroupName($permissionName);
                $permission = $this->em->findOneBy('App:SysPermissionLabel',['pname'=>$permissionName]);
                if(!is_null($permission))
                    $grp->setLabel($permission->getPlabel());
                else
                    $grp->setLabel($permissionName);
                $this->em->insertEntity($grp);

            }
            if(array_search($grp->getId(),$groups) !== false){
                return true;
            }

        }
        return false;
    }

    public function positionsOfGroup($groupID)
    {
        $obj = $this->em->getORM();
        return  $obj->getRepository('App:SysPosition')->createQueryBuilder('r')
            ->Where('r.groups = :group')
            ->orWhere('r.groups LIKE :group1')
            ->orWhere('r.groups LIKE :group2')
            ->orWhere('r.groups LIKE :group3')
            ->setParameter('group',  $groupID)
            ->setParameter('group1', '%,' . $groupID . ',%')
            ->setParameter('group2', '%' . $groupID . ',%')
            ->setParameter('group3', '%,' . $groupID . '%')
            ->getQuery()
            ->getResult();
    }

    public function removeFromGroup($positionID,$groupID)
    {
        $position = $this->em->find('App:SysPosition',$positionID);
        $arrayRolls = explode(',',$position->getGroups());
        if(in_array($groupID,$arrayRolls)){
            $itemKey = array_search($groupID,$arrayRolls);
            unset($arrayRolls[$itemKey]);
            $newGroupList = implode(',',$arrayRolls);
            $position->setGroups($newGroupList);
            $this->em->update($position);
        }
    }

    public function addToGroup($posionID,$groupID)
    {
        $obj = $this->em->getORM();
        $position = $obj->getRepository('App:SysPosition')->find($posionID);
        $groups = explode(',',$position->getGroups());
        if(! array_search($groupID,$groups)){
            array_push($groups,$groupID);
            $newGroupList = implode(',',$groups);
            $position->setGroups($newGroupList);
            $obj->persist($position);
            $obj->flush();
        }
    }

    //--------------- NOTIFICATION CONTROLL ---------------
    public function notificationCount()
    {
        if(! $this->isLogedIn())
            return 0;
        return count($this->em->findBy('App:SysNotification',['userID'=>$this->currentPosition(),'viewed'=>null]));
    }

    public function lastNotifications($count=10)
    {
        $orm = $this->em->getORM();
        return $orm->createQueryBuilder('q')
            ->select('q')
            ->from('App:SysNotification','q')
            ->setMaxResults($count)
            ->orderBy('q.id','DESC')
            ->getQuery()
            ->execute();
    }

    public function addNotificationForUser($user,$des,$url){
        $notification = new Entity\SysNotification();
        $notification->setDateSubmit(time());
        $notification->setUserID($user);
        $notification->setViewed(null);
        $notification->setDes($des);
        $notification->setLinkTarget($url);
        return $this->em->insertEntity($notification);
    }


    public function addNotificationForGroup($GroupName,$boundle,$des,$url,$pid=1){
        $group = $this->em->findOneBy('App:SysGroup',['groupName'=>$GroupName,'bundle'=>$boundle,'PID'=>$pid]);
        $users = $this->positionsOfGroup($group->getId());
        foreach ($users as $user)
        {
            $notification = new Entity\SysNotification();
            $notification->setDateSubmit(time());
            $notification->setUserID($user);
            $notification->setViewed(null);
            $notification->setDes($des);
            $notification->setLinkTarget($url);
            return $this->em->insertEntity($notification);
        }
    }
}