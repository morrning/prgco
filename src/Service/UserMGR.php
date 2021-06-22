<?php
namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\BrowserKit\CookieJar;
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
        if(isset($_COOKIE['SYSUSER']))
            $sessionID = $_COOKIE['SYSUSER'];

        $this->user = $this->em->findOneBy('App:SysUser',['uniqueID'=>$sessionID]);
        return $this->user;
    }

    public function login($username, $password,$remember=false){

        $params = ["username"=>$username,"password"=>md5($password)];
        $user = $this->em->findOneBy('App:SysUser',$params);
        if(! is_null($user))
        {
            $user->setUniqueID($this->session->getId());
            $this->em->update($user);
            if($remember){
                $config = $this->em->find('App:SysConfig',1);
                setcookie('SYSUSER', $user->getUniqueID(), time() + (86400 * $config->getUSERSMAXCOOKIETIME()), "/"); // 86400 = 1 day
            }
            return true;
        }
        return false;
    }

    public function logout(){
        if($this->isLogedIn()){
            $user = $this->currentUser();
            $user->setUniqueID('');
            setcookie('SYSUSER', '', time() - 86400 , "/"); // 86400 = 1 day
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
        $bundleState = $this->em->findOneBy('App:SysBundle',['bundleName'=>$bundle]);
        if(! is_null($bundleState))
            if($bundleState->getIsDisabled() == true)
                return false;

        if($permissionName != 'superAdmin'){
            if($this->hasPermission('superAdmin')){
                return true;
            }
        }
        if($this->isLogedIn()){
            $groups = explode(',',$this->currentPosition()->getPermissionGroups());
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

            //check for position roll
            if(!is_null($this->currentPosition()->getRoll()))
            {
                $property = 'get' . ucfirst($permissionName);
                $metaData = $this->em->getORM()->getClassMetadata(Entity\SysRoll::class);
                if ($metaData->hasField($permissionName)) {
                    //property exists
                    if($this->currentPosition()->getRoll()->$property() == 1)
                        return true;
                }
            }
        }
        return false;
    }

    public function positionsOfGroup($groupID)
    {
        $obj = $this->em->getORM();
        $singlePermissionResult =  $obj->getRepository('App:SysPosition')->createQueryBuilder('r')
            ->Where('r.permissiongroups = :group')
            ->orWhere('r.permissiongroups LIKE :group1')
            ->orWhere('r.permissiongroups LIKE :group2')
            ->orWhere('r.permissiongroups LIKE :group3')
            ->setParameter('group',  $groupID)
            ->setParameter('group1', '%,' . $groupID . ',%')
            ->setParameter('group2', '%' . $groupID . ',%')
            ->setParameter('group3', '%,' . $groupID . '%')
            ->getQuery()
            ->getResult();
        foreach ($singlePermissionResult as $item)
            $item->setPermissionFromRoll(false);

        $group = $this->em->find('App:SysGroup',$groupID);

        $rolls = $this->em->findBy('App:SysRoll',[$group->getGroupName()=>true]);
        $rollRes=[];
        foreach ($rolls as $roll){
            $temp = $this->em->findBy('App:SysPosition',['roll'=>$roll,'defaultArea'=>$group->getPID()]);
            if(! is_null($temp))
                foreach ($temp as $tmp)
                    array_push($rollRes,$tmp);
        }
        foreach ($rollRes as $item)
            $item->setPermissionFromRoll(true);

        return array_merge($singlePermissionResult,$rollRes);
    }

    public function removeFromGroup($positionID,$groupID)
    {
        $position = $this->em->find('App:SysPosition',$positionID);
        $arrayRolls = explode(',',$position->getPermissiongroups());
        if(in_array($groupID,$arrayRolls)){
            $itemKey = array_search($groupID,$arrayRolls);
            unset($arrayRolls[$itemKey]);
            $newGroupList = implode(',',$arrayRolls);
            $position->setPermissionGroups($newGroupList);
            $this->em->update($position);
        }
    }

    public function addToGroup($posionID,$groupID)
    {
        $obj = $this->em->getORM();
        $position = $obj->getRepository('App:SysPosition')->find($posionID);
        $groups = explode(',',$position->getPermissiongroups());
        if(! array_search($groupID,$groups)){
            array_push($groups,$groupID);
            $newGroupList = implode(',',$groups);
            $position->setPermissionGroups($newGroupList);
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
            ->where('q.userID=?1 AND q.viewed IS NULL')
            ->setParameter('1',$this->currentPosition())
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
        if(is_null($group)) return false;
        $users = $this->positionsOfGroup($group->getId());
        foreach ($users as $user)
        {
            $notification = new Entity\SysNotification();
            $notification->setDateSubmit(time());
            $notification->setUserID($user);
            $notification->setViewed(null);
            $notification->setDes($des);
            $notification->setLinkTarget($url);
            $this->em->insertEntity($notification);
        }
        return true;
    }

    public function sendSmsToGroup($GroupName,$boundle,$des,$pid=1){
        $config = $this->em->findAll('App:SysConfig');
        $config = $config[0];

        $group = $this->em->findOneBy('App:SysGroup',['groupName'=>$GroupName,'bundle'=>$boundle,'PID'=>$pid]);
        if(is_null($group)) return false;
        $users = $this->positionsOfGroup($group->getId());
        $des = $des . '.' . $config->getSiteName();
        $userNums = [];
        foreach ($users as $user)
        {
            array_push($userNums,$user->getUserID()->getMobileNum());
        }
        $webServiceURL  = "http://sms.parsgreen.ir/Apiv2/Message/SendSms";
        $req= new \App\DataObject\SendSms();
        $req-> SmsBody  =$des;
        $req-> Mobiles = $userNums;

        $ch = curl_init($webServiceURL);
        $jsonDataEncoded = json_encode($req);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $header =array('authorization: BASIC APIKEY:'. $config->getSMSAPI(),'Content-Type: application/json;charset=utf-8');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        $result = curl_exec($ch);
        $res = json_decode($result);
        curl_close($ch);
        return true;
    }
    public function sendSmsToUser($user,$des){
        $config = $this->em->findAll('App:SysConfig');
        $config = $config[0];
        $des = $des . ' ' . $config->getSiteName();

        $webServiceURL  = "http://sms.parsgreen.ir/Apiv2/Message/SendSms";
        $req= new \App\DataObject\SendSms();
        $req-> SmsBody  =$des;
        $req-> Mobiles = [$user->getUserID()->getMobileNum()];

        $ch = curl_init($webServiceURL);
        $jsonDataEncoded = json_encode($req);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $header =array('authorization: BASIC APIKEY:'. $config->getSMSAPI(),'Content-Type: application/json;charset=utf-8');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        $result = curl_exec($ch);
        $res = json_decode($result);
        curl_close($ch);
        return true;
    }
}
