<?php
/**
 * Created by PhpStorm.
 * User: PKPU-26
 * Date: 3/8/2019
 * Time: 7:17 AM
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity as Entity;

class ConfigMGR
{
    protected $em;
    protected $config;

    function __construct(EntityMGR $entityMgr)
    {
        $this->em = $entityMgr;
        $this->config = $this->em->find('App:SysConfig',1);
        if(is_null($this->config))
        {
            $config = new Entity\SysConfig();
            $config->setSiteName('YOUR SITE NAME');
            $this->em->insertEntity($config);
            $this->config = $config;
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function importBasicData(){
        //IMPORT permission names
        if(count($this->em->findAll('App:SysPermissionLabel')) == 0){
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('superAdmin');
            $pl->setPlabel('مدیر کل سامانه');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('newsPublish');
            $pl->setPlabel('مدیریت انتشار خبر');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('MostUsedFiles');
            $pl->setPlabel('مدیریت فایل‌های پراستفاده');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('ictRequest');
            $pl->setPlabel('درخواست خدمات فناوری اطلاعات و ارتباطات');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('ictDoing');
            $pl->setPlabel('مجری خدمات فناوری اطلاعات و ارتباطات');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('CeremonailREQ');
            $pl->setPlabel('درخواست تشریفات');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('phonebookAdmin');
            $pl->setPlabel('مدیریت دفترتلفن');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('suggestionInbox');
            $pl->setPlabel('مدیریت نظام پیشنهادات و انتقادات');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('ISOfORMS');
            $pl->setPlabel('مدیریت سامانه IMS');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('CountDown');
            $pl->setPlabel('مدیریت روزشمار رویدادها');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('CeremonailMNGDashboard');
            $pl->setPlabel('مدیریت سامانه تشریفات');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('CeremonailOPTDashboard');
            $pl->setPlabel('اپراتوری سامانه تشریفات');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('projectAdmin');
            $pl->setPlabel('مدیریت کنترل پروژه کل');
            $this->em->insertEntity($pl);
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('pmAccess');
            $pl->setPlabel('مدیریت کنترل پروژه ناحیه');
            $this->em->insertEntity($pl);
        }

        //import default daytime
        if(count($this->em->findAll('App:CMdaytime')) == 0){
            $daytime = new Entity\CMdaytime();
            $daytime->setLabel('اهمیتی ندارد');
            $this->em->insertEntity($daytime);
            $daytime = new Entity\CMdaytime();
            $daytime->setLabel('صبح(6-12)');
            $this->em->insertEntity($daytime);
            $daytime = new Entity\CMdaytime();
            $daytime->setLabel('عصر(18-13)');
            $this->em->insertEntity($daytime);
            $daytime = new Entity\CMdaytime();
            $daytime->setLabel('شب(24-18)');
            $this->em->insertEntity($daytime);
        }

        //import default moneys
        if(count($this->em->findAll('App:ACCMoney')) == 0){
            $money = new Entity\ACCMoney();
            $money->setMoneyName('ریال ایران');
            $this->em->insertEntity($money);
            $money = new Entity\ACCMoney();
            $money->setMoneyName('دلار آمریکا');
            $this->em->insertEntity($money);
            $money = new Entity\ACCMoney();
            $money->setMoneyName('دینار عراق');
            $this->em->insertEntity($money);
        }

        //import default moneys
        if(count($this->em->findAll('App:ACCiccenter')) == 0){
            $entity = new Entity\ACCiccenter();
            $entity->setIccode(1001);
            $entity->setIcname('نیروی انسانی');
            $this->em->insertEntity($entity);

            $sentity = new Entity\ACCiccenter();
            $sentity->setParent($entity);
            $sentity->setIcname('ایاب و ذهاب');
            $sentity->setIccode(1002);
            $this->em->insertEntity($sentity);
        }

        //import CM default accept if conjections
        if(count($this->em->findAll('App:CMacceptIF')) == 0) {
            $entity = new Entity\CMacceptIF();
            $entity->setIfCode(1);
            $entity->setIfName('پرداخت از حساب شرکت');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMacceptIF();
            $entity->setIfCode(2);
            $entity->setIfName('بدهکار کردن درخواست کننده');
            $this->em->insertEntity($entity);
        }

        //import CM default Airways
        if(count($this->em->findAll('App:CMAirway')) == 0) {
            $entity = new Entity\CMAirway();
            $entity->setAirwayName('ماهان ایر');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMAirway();
            $entity->setAirwayName('قشم ایر');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMAirway();
            $entity->setAirwayName('تابان');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMAirway();
            $entity->setAirwayName('ایران ایر');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMAirway();
            $entity->setAirwayName('IRAQI Airways');
            $this->em->insertEntity($entity);
        }

        //import CM default Ticket state
        if(count($this->em->findAll('App:CMAirTicketState')) == 0) {
            $entity = new Entity\CMAirTicketState();
            $entity->setStateCode(0);
            $entity->setStateName('در انتظار بررسی');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMAirTicketState();
            $entity->setStateCode(1);
            $entity->setStateName('رد شده');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMAirTicketState();
            $entity->setStateCode(2);
            $entity->setStateName('تایید شده');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMAirTicketState();
            $entity->setStateCode(3);
            $entity->setStateName('خریداری شده');
            $this->em->insertEntity($entity);

        }

        //import CM default cities
        if(count($this->em->findAll('App:CMCities')) == 0) {
            $entity = new Entity\CMCities();
            $entity->setCname('تهران - امام خمینی(ره)');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMCities();
            $entity->setCname('تهران - مهرآباد');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMCities();
            $entity->setCname('مشهد');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMCities();
            $entity->setCname('اصفهان');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMCities();
            $entity->setCname('تبریز');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMCities();
            $entity->setCname('نجف');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMCities();
            $entity->setCname('بغداد');
            $this->em->insertEntity($entity);

        }

        //import CM default passenger type
        if(count($this->em->findAll('App:CMPassengerType')) == 0) {
            $entity = new Entity\CMPassengerType();
            $entity->setTypeName('پرسنل شرکت');
            $entity->setHseGuide(0);
            $this->em->insertEntity($entity);
            $entity = new Entity\CMPassengerType();
            $entity->setTypeName('خویشاوند پرسنل شرکت');
            $entity->setHseGuide(0);
            $this->em->insertEntity($entity);
            $entity = new Entity\CMPassengerType();
            $entity->setTypeName('پرسنل پیمانکار');
            $entity->setHseGuide(1);
            $this->em->insertEntity($entity);
            $entity = new Entity\CMPassengerType();
            $entity->setTypeName('بازدیدکننده');
            $entity->setHseGuide(1);
            $this->em->insertEntity($entity);
        }

        //import ICT default CPU type
        if(count($this->em->findAll('App:ICTCpuType')) == 0) {
            $entity = new Entity\ICTCpuType();
            $entity->setTypeName('INTEL CELERON');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTCpuType();
            $entity->setTypeName('INTEL PENTIUM');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTCpuType();
            $entity->setTypeName('INTEL DOUL CORE');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTCpuType();
            $entity->setTypeName('INTER ATOM');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTCpuType();
            $entity->setTypeName('INTEL CORE I3');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTCpuType();
            $entity->setTypeName('INTEL CORE I5');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTCpuType();
            $entity->setTypeName('INTEL CORE I7');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTCpuType();
            $entity->setTypeName('AMD');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTCpuType();
            $entity->setTypeName('ARM');
            $this->em->insertEntity($entity);
        }
    }
}