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
            $config->setSiteName('نام شرکت شما...');
            $config->setUSERSMAXCOOKIETIME(1);
            $this->em->insertEntity($config);
            $this->config = $config;
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function importBasicData(){
        //import default hoteling WC type
        if(count($this->em->findAll('App:DTyesNo')) == 0){
            $entity = new Entity\DTyesNo();
            $entity->setTypeCode(1);
            $entity->setTypeName('بله');
            $this->em->insertEntity($entity);
            $entity = new Entity\DTyesNo();
            $entity->setTypeCode(0);
            $entity->setTypeName('خیر');
            $this->em->insertEntity($entity);
        }


        //import iso forms type
        if(count($this->em->findAll('App:StaticMonth')) == 0) {
            $entity = new Entity\StaticMonth();
            $entity->setCode(1);
            $entity->setLabel('فروردین');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(2);
            $entity->setLabel('اردیبهشت');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(3);
            $entity->setLabel('خرداد');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(4);
            $entity->setLabel('تیر');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(5);
            $entity->setLabel('مرداد');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(6);
            $entity->setLabel('شهریور');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(7);
            $entity->setLabel('مهر');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(8);
            $entity->setLabel('آبان');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(9);
            $entity->setLabel('آذر');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(10);
            $entity->setLabel('دی');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(11);
            $entity->setLabel('بهمن');
            $this->em->insertEntity($entity);
            $entity = new Entity\StaticMonth();
            $entity->setCode(12);
            $entity->setLabel('اسفند');
            $this->em->insertEntity($entity);
        }

        //IMPORT permission names
        //IMPORT permission names
        if(is_null($this->em->findOneBy('App:SysPermissionLabel',['pname'=>'CMOPTARBAEIN']))) {
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('CMOPTARBAEIN');
            $pl->setPlabel('اپراتوری سامانه اربعین');
            $this->em->insertEntity($pl);
        }
        if(is_null($this->em->findOneBy('App:SysPermissionLabel',['pname'=>'HRMACCESS']))) {
            $pl = new Entity\SysPermissionLabel();
            $pl->setPname('HRMACCESS');
            $pl->setPlabel('مدیریت منابع انسانی');
            $this->em->insertEntity($pl);
        }
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
        $entity = $this->em->findOneBy('App:SysPermissionLabel',['pname'=>'HSSETOTAL']);
        if(is_null($entity)){
            $entity = new Entity\SysPermissionLabel();
            $entity->setPname('HSSETOTAL');
            $entity->setPlabel('سامانه جامع ایمنی و اقدامات تامینی');
            $this->em->insertEntity($entity);
        }
        $entity = $this->em->findOneBy('App:SysPermissionLabel',['pname'=>'HSSEAREA']);
        if(is_null($entity)){
            $entity = new Entity\SysPermissionLabel();
            $entity->setPname('HSSEAREA');
            $entity->setPlabel('سامانه ناحیه‌ای ایمنی و اقدامات تامینی');
            $this->em->insertEntity($entity);
        }

        //import iso forms type
        if(count($this->em->findAll('App:ISOFormType')) == 0) {
            $entity = new Entity\ISOFormType();
            $entity->setTypeName('فرم اجرایی');
            $entity->setTypeCode(1);
            $this->em->insertEntity($entity);
            $entity = new Entity\ISOFormType();
            $entity->setTypeName('دستورالعمل');
            $entity->setTypeCode(2);
            $this->em->insertEntity($entity);
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
            $money->setMoneyCode('IRR');
            $this->em->insertEntity($money);
            $money = new Entity\ACCMoney();
            $money->setMoneyName('دلار آمریکا');
            $money->setMoneyCode('DLR');
            $this->em->insertEntity($money);
            $money = new Entity\ACCMoney();
            $money->setMoneyName('دینار عراق');
            $money->setMoneyCode('IQD');
            $this->em->insertEntity($money);
        }
        //import default acc account
        if(count($this->em->findAll('App:ACCaccount')) == 0){
            $entity = new Entity\ACCaccount();
            $entity->setAccountNo('1');
            $entity->setLabel('حساب اصلی شرکت');
            $this->em->insertEntity($entity);
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

        //import default Visa countries
        if(count($this->em->findAll('App:CMVisaCountry')) == 0){
            $entity = new Entity\CMVisaCountry();
            $entity->setCountryCode(964);
            $entity->setCountryName('عراق');
            $this->em->insertEntity($entity);
        }

        //import default Visa countries
        if(count($this->em->findAll('App:CMVisaType')) == 0){
            $entity = new Entity\CMVisaType();
            $entity->setTypeCode(1);
            $entity->setTypeName('زیارتی و توریستی');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMVisaType();
            $entity->setTypeCode(2);
            $entity->setTypeName('تجاری');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMVisaType();
            $entity->setTypeCode(3);
            $entity->setTypeName('تجاری متعدده');
            $this->em->insertEntity($entity);
        }

        //import default Visa send ways
        if(count($this->em->findAll('App:CMVisaSendWay')) == 0){
            $entity = new Entity\CMVisaSendWay();
            $entity->setWayCode(1);
            $entity->setWayName('تحویل به دفتر شرکت');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMVisaSendWay();
            $entity->setWayCode(2);
            $entity->setWayName('ارسال توسط شرکت پست');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMVisaSendWay();
            $entity->setWayCode(3);
            $entity->setWayName('ارسال توسط تیپاکس');
            $this->em->insertEntity($entity);
        }

        //import default Visa states
        if(count($this->em->findAll('App:CMVisaState')) == 0){
            $entity = new Entity\CMVisaState();
            $entity->setStateCode(-1);
            $entity->setStateName('رد از طرف مدیریت');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMVisaState();
            $entity->setStateCode(0);
            $entity->setStateName('در انتظار تایید');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMVisaState();
            $entity->setStateCode(1);
            $entity->setStateName('تایید شده توسط مدیریت');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMVisaState();
            $entity->setStateCode(2);
            $entity->setStateName('انجام شده');
            $this->em->insertEntity($entity);
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

        //import CM default passenger documents type
        if(count($this->em->findAll('App:CMPassengerDocType')) == 0) {
            $entity = new Entity\CMPassengerDocType();
            $entity->setTname('شناسنامه');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMPassengerDocType();
            $entity->setTname('کارت ملی');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMPassengerDocType();
            $entity->setTname('گذرنامه');
            $this->em->insertEntity($entity);
        }
        //import CM default Ticket state
        if(count($this->em->findAll('App:CMAirTicketState')) == 0) {
            $entity = new Entity\CMAirTicketState();
            $entity->setStateCode(0);
            $entity->setStateName('در انتظار بررسی');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMAirTicketState();
            $entity->setStateCode(-1);
            $entity->setStateName('رد شده');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMAirTicketState();
            $entity->setStateCode(1);
            $entity->setStateName('تایید شده');
            $this->em->insertEntity($entity);
            $entity = new Entity\CMAirTicketState();
            $entity->setStateCode(2);
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
        //import default hoteling WC type
        if(count($this->em->findAll('App:HotelingRoomWCType')) == 0){
            $entity = new Entity\HotelingRoomWCType();
            $entity->setTypeCode(1);
            $entity->setTypeName('توالت ایرانی');
            $this->em->insertEntity($entity);
            $entity = new Entity\HotelingRoomWCType();
            $entity->setTypeCode(2);
            $entity->setTypeName('توالت فرنگی');
            $this->em->insertEntity($entity);
        }

        //import CM default passenger type
        if(count($this->em->findAll('App:CMPassengerType')) == 0) {
            $entity = new Entity\CMPassengerType();
            $entity->setTypeName('پرسنل شرکت');
            $entity->setHseGuide(0);
            $this->em->insertEntity($entity);
            $entity = new Entity\CMPassengerType();
            $entity->setTypeName('خانواده');
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

        //import ICT DOING STATE
        if(count($this->em->findAll('App:ICTRequestEMSState')) == 0) {
            $entity = new Entity\ICTRequestEMSState();
            $entity->setStateName('عادی');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTRequestEMSState();
            $entity->setStateName('فوری');
            $this->em->insertEntity($entity);
        }

        //import ICT REQUEST TYPE
        if(count($this->em->findAll('App:ICTRequestType')) == 0) {
            $entity = new Entity\ICTRequestType();
            $entity->setTypeName('نصب');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTRequestType();
            $entity->setTypeName('تعمیر و رفع عیب');
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTRequestType();
            $entity->setTypeName('به روز رسانی');
            $this->em->insertEntity($entity);
        }

        if(count($this->em->findAll('App:ICTRequestState')) == 0) {
            $entity = new Entity\ICTRequestState();
            $entity->setStateName('در حال بررسی');
            $entity->setStateCode(0);
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTRequestState();
            $entity->setStateName('پاسخ داده شده');
            $entity->setStateCode(1);
            $this->em->insertEntity($entity);
            $entity = new Entity\ICTRequestState();
            $entity->setStateName('خاتمه یافته');
            $entity->setStateCode(2);
            $this->em->insertEntity($entity);
        }
        //config max cookie time
        if(is_null($this->config->getUSERSMAXCOOKIETIME())){
            $this->config->setUSERSMAXCOOKIETIME(1);
            $this->em->update($this->config);
        }
        //import acc account default
        if(count($this->em->findAll('App:ACCaccount')) == 0) {
            $entity = new Entity\ACCaccount();
            $entity->setLabel('حساب اصلی شرکت');
            $this->em->insertEntity($entity);
        }
        //import Menu default data
        if(count($this->em->findAll('App:SysMenu')) == 0) {
            $entity = new Entity\SysMenu();
            $entity->setMenuName('MAIN');
            $entity->setMenuLabel('منو اصلی سیستم');
            $this->em->insertEntity($entity);
            $entityItem = new Entity\SysMenuItem();
            $entityItem->setLabel('سامانه مکاتبات سازمانی');
            $entityItem->setFontawsome('<i class="fas fa-envelope"></i>');
            $entityItem->setInternalUrl(false);
            $entityItem->setUpper(1);
            $entityItem->setMenu($entity);
            $entityItem->setUrl('https://oas.prgco.ir/cas/login?service=http%3A%2F%2Foas.prgco.ir%2Fmenu%2FIndex.do');
            $this->em->insertEntity($entityItem);
            $entityItem = new Entity\SysMenuItem();
            $entityItem->setLabel('سامانه فیش حقوقی');
            $entityItem->setFontawsome('<i class="fas fa-money-check-alt"></i>');
            $entityItem->setInternalUrl(false);
            $entityItem->setUpper(2);
            $entityItem->setMenu($entity);
            $entityItem->setUrl('http://pay.prgco.ir');
            $this->em->insertEntity($entityItem);
            $entityItem = new Entity\SysMenuItem();
            $entityItem->setLabel('سامانه PMS');
            $entityItem->setFontawsome('<i class="fab fa-product-hunt"></i>');
            $entityItem->setInternalUrl(false);
            $entityItem->setUpper(3);
            $entityItem->setMenu($entity);
            $entityItem->setUrl('http://pms.prgco.ir');
            $this->em->insertEntity($entityItem);
            $entityItem = new Entity\SysMenuItem();
            $entityItem->setLabel('سامانه مدیریت منابع انسانی');
            $entityItem->setFontawsome('<i class="fas fa-user-alt"></i>');
            $entityItem->setInternalUrl(false);
            $entityItem->setUpper(4);
            $entityItem->setMenu($entity);
            $entityItem->setUrl('http://pw.prgco.ir:1210/Account/Login');
            $this->em->insertEntity($entityItem);
            $entityItem = new Entity\SysMenuItem();
            $entityItem->setLabel('پست الکترونیکی');
            $entityItem->setFontawsome('<i class="fas fa-inbox"></i>');
            $entityItem->setInternalUrl(false);
            $entityItem->setUpper(5);
            $entityItem->setMenu($entity);
            $entityItem->setUrl('https://mail.prgco.ir/');
            $this->em->insertEntity($entityItem);
        }

        //import Menu default data
        if(count($this->em->findAll('App:SysBundle')) == 0) {
            $entity = new Entity\SysBundle();
            $entity->setBundleName('ICT');
            $entity->setDes('مدیریت خدمات فناوری اطلاعات و ارتباطات و پشتیبانی ذی نفعان');
            $entity->setIsDisabled(false);
            $this->em->insertEntity($entity);
            $entity = new Entity\SysBundle();
            $entity->setBundleName('CEREMONIAL');
            $entity->setDes('مدیریت تشریفات سازمانی');
            $entity->setIsDisabled(false);
            $this->em->insertEntity($entity);
        }
    }
}