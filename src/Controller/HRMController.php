<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Form\Type as Type;

use App\Service;
use App\Entity;
class HRMController extends AbstractController
{
    /**
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 32) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * @Route("/hrm/report/user/io", name="HRMReportUserIO")
     */
    public function HRMReportUserIO(Request $request,Service\MssqlMGR $mssqlMGR,Service\ConfigMGR $configMGR,Service\UserMGR $userMGR,Service\Jdate $jdate)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        $siteConfig = $configMGR->getConfig();
        $mssqlMGR->configure($siteConfig->getHRMPWSERVERNAME(),$siteConfig->getHRMPWDATABASE(),$siteConfig->getHRMPWUSERNAME(),$siteConfig->getHRMPWPASSWORD());
        //get years from SG SERVER
        $conn = $mssqlMGR->getConnection();
        if(is_null($conn))
            return $this->redirectToRoute('500');


        $selectQuery1 = "SELECT * FROM DataFile WHERE (Emp_No = ?) ORDER BY Date ASC";
        $stmt = $conn->prepare($selectQuery1);
        $stmt->bindValue(1, $userMGR->currentUser()->getEmployeNum());
        $stmt->execute();
        $ioData = $stmt->fetchAll();

        //create Date Array
        $daySec = 24 * 60 * 60;
        $days =[];
        for($i = 31;$i > 0; $i --){
            $dayStr = $jdate->jdate('Ymd',time() - ($i * $daySec));
            $temp =[];
            foreach ($ioData as $io){
                if($io['Date'] == $dayStr)
                    array_push($temp,$io);
            }
            $days[$jdate->jdate('Y/m/d',time() - ($i * $daySec))] = $temp;
        }
        return $this->render('hrm/employe/io.html.twig', [
            'days'  =>  $days
        ]);
    }


    /**
     * @Route("/hrm/report/earn", name="HRMReportEarn")
     */
    public function HRMReportEarn(Request $request,Service\MssqlMGR $mssqlMGR,Service\ConfigMGR $configMGR,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        $siteConfig = $configMGR->getConfig();
        $mssqlMGR->configure($siteConfig->getHRMSGSERVERNAME(),$siteConfig->getHRMSGDATABASE(),$siteConfig->getHRMSGUSERNAME(),$siteConfig->getHRMSGPASSWORD());
        //get years from SG SERVER
        $conn = $mssqlMGR->getConnection();
        if(is_null($conn))
            return $this->redirectToRoute('500');

        $str = 'Select FYear from GNR.GenYears ORDER BY FYear DESC';
        $stmt = $conn->query($str);
        $yesrs = $stmt->fetchAll();
        $yearsArray = [];
        foreach ($yesrs as $yesr){
            $yearsArray['13' . $yesr['FYear'] ]= '13' . $yesr['FYear'];
        }
        $combs = ['message'=>'test'];
        $form = $this->createFormBuilder($combs)
            ->add('monthNames', EntityType::class, [
                'class'=>Entity\StaticMonth::class,
                'choice_label'=>'label',
                'choice_value' => 'code',
                'label'=>'ماه'
            ])
            ->add('years', ChoiceType::class, [
                'choices'=>$yearsArray,
                'label'=>'ماه'
            ])
            ->add('submit', SubmitType::class,['label'=>'جستوجوی فیش حقوقی'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        $fish = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $selectQuery1 = "SELECT * FROM tbuser WHERE (nationalCode = ?)";
            $stmt = $conn->prepare($selectQuery1);
            $stmt->bindValue(1, $userMGR->currentUser()->getNationalCode());
            $stmt->execute();
            $userInExternalDb = $stmt->fetch();
            $selectQuery1 =  "SELECT HRM.element.title,HRM.element.type, elmntref,val,issueyear,issuemonth,EffectYear,EffectMonth 
                            FROM HRM.element,HRM.payMVPers 
                            WHERE HRM.element.serial=HRM.payMVPers.elmntref AND val<>0 AND persref=? AND  issueyear=? and issuemonth=? ";
            $stmt = $conn->prepare($selectQuery1);
            $stmt->bindValue(1, $userInExternalDb['Serial']);
            $stmt->bindValue(2, $form->get('years')->getData());
            $stmt->bindValue(3, $form->get('monthNames')->getData()->getCode());
            $stmt->execute();
            $fish = $stmt->fetchAll();
            if(count($fish) == 0)
                $fish = 0;
        }
        return $this->render('hrm/reportEarn.html.twig', [
            'form' => $form->createView(),
            'hrmUser' =>$userMGR->currentUser(),
            'alert' =>$alerts,
            'fish'=>$fish
        ]);
    }

    /**
     * @Route("/hrm/admin", name="HRMadmin")
     */
    public function HRMadmin(Service\EntityMGR $entityMGR, Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');
        $positions = $entityMGR->findAll('App:SysPosition');
        $users = [];
        foreach ($positions as $position){
            if(array_search($position->getUserID(),$users) == false){
                array_push($users,$position->getUserID());
            }
        }
        $info['allUsers'] = count($users);
        $constractors = $entityMGR->findBy('App:SysPosition',['constractor'=>1]);
        $users = [];
        foreach ($constractors as $constractor){
            if(array_search($constractor->getUserID(),$users) == false){
                array_push($users,$constractor->getUserID());
            }
        }
        $info['contractor'] = count($users);
        $info['employers'] =  $info['allUsers'] - $info['contractor'];
        $info['contractorPassenger'] = 0;
        $ptype = $entityMGR->findOneBy('App:CMPassengerType',['typeName'=>'پرسنل پیمانکار']);
        foreach ($constractors as $constractor){
            $info['contractorPassenger'] = $info['contractorPassenger'] + count($entityMGR->findBy('App:CMPassenger',['submitter'=>$constractor,'ptype'=>$ptype]));
        }
        $info['contractorPassenger'] = $info['contractorPassenger'] - $info['contractor'];
        return $this->render('hrm/dashboard.html.twig', [
            'info'=> $info,
        ]);
    }


    /**
     * @Route("/hrm/positions/list", name="HRMpositionsList")
     */
    public function HRMpositionsList(Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');

        return $this->render('hrm/positions.html.twig', [
            'users' => $entityMGR->findBy('App:SysPosition')
        ]);
    }
    /**
     * @Route("/hrm/position/folder/{id}/{msg}", name="HRMPositionFolder")
     */
    public function HRMPositionFolder($id,$msg=0,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');
        $user = $entityMGR->find('App:SysPosition',$id);
        if(is_null($user))
            return $this->redirectToRoute('404');

        $alerts = [];
        if($msg === 'letter_add')
            array_push($alerts,['type'=>'success','message'=>'مکاتبه با موفقیت ثبت شد.']);

        return $this->render('hrm/positionFolder.html.twig', [
            'user' => $user,
            'passengers'=>$entityMGR->findBy('App:CMPassenger',['submitter'=>$user])
        ]);
    }

    /**
     * @Route("/hrm/employe/list/{msg}", name="HRMEmployelist")
     */
    public function employes($msg=0,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');

        $users = [];
        $poUsers = $entityMGR->findAll('App:SysUser');
        foreach ($poUsers as $poUser){
            $uofs = $entityMGR->findBy('App:SysPosition',['userID'=>$poUser,'constractor'=>0]);
            foreach ($uofs as $uof){
                if(array_search($poUser,$users) == false){
                    array_push($users,$poUser);
                }
            }
        }
        return $this->render('hrm/employes.html.twig', [
            'users' => $users,
            'msg' => $msg
        ]);
    }

    /**
     * @Route("/hrm/employe/folder/{id}/{msg}", name="HRMEmployeFolder")
     */
    public function HRMEmployeFolder($id,$msg=0,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');
        $user = $entityMGR->find('App:SysUser',$id);
        if(is_null($user))
            return $this->redirectToRoute('404');

        $alerts = [];
        if($msg === 'letter_add')
            array_push($alerts,['type'=>'success','message'=>'مکاتبه با موفقیت ثبت شد.']);

        return $this->render('hrm/employeFolder.html.twig', [
            'user' => $user,
            'letters' => $entityMGR->findBy('App:HRMLetterOutCountry',['user'=>$user]),
            'alerts' => $alerts,
            'passengers'=>$entityMGR->findBy('App:CMPassenger',['submitter'=>$user])
        ]);
    }

    /**
     * @Route("/hrm/employe/letteroutcountry/new/{id}", name="HRMEmployeLetterOutCountryNew")
     */
    public function HRMEmployeLetterOutCountryNew(Request $request, $id,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');
        $user = $entityMGR->findOneBy('App:SysUser',['nationalCode'=>$id]);
        if(is_null($user))
            return $this->redirectToRoute('404');

        $letter = new Entity\HRMLetterOutCountry();
        $form = $this->createForm(\App\Form\HRMLetterOutCountryType::class,$letter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $letter->setUser($user);
            $entityMGR->insertEntity($letter);
            return $this->redirectToRoute('HRMEmployeFolder',['id'=>$user->getId(), 'msg'=>'letter_add']);
        }
        return $this->render('hrm/employeLetterCountryOutAdd.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/hrm/air/ticket/list", name="HRMAirTicketList")
     */
    public function HRMAirTicketList(Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');

        return $this->render('hrm/ticket/airTicketRequests.html.twig', [
            'tickets' => $entityMGR->findBy('App:CMAirTicket',[],['dateSubmit'=>'DESC']),
        ]);
    }

    /**
     * @Route("/hrm/air/ticket/view/{id}", name="HRMAirTicketView")
     */
    public function HRMAirTicketView($id, Request $request, Service\UserMGR $userMGR,Service\EntityMGR $entityMGR,Service\LogMGR $logMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');
        $ticket = $entityMGR->find('App:CMAirTicket',$id);
        if(is_null($ticket))
            return $this->redirectToRoute('404');

        $passengers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$ticket->getCmlist()]);
        $logMGR->addEvent('CERTICKET'.$ticket->getId(),'مشاهده','اطلاعات درخواست بلیط','CEREMONIAL',$request->getClientIp());

        return $this->render('hrm/ticket/airTicketView.html.twig', [
            'passengers' => $passengers,
            'ticket'=>$ticket,
        ]);
    }

    /**
     * @Route("/hrm/visa/reqs/list", name="HRMVisaList")
     */
    public function HRMVisaList(Request $request, Service\UserMGR $userMGR,Service\EntityMGR $entityMGR,Service\LogMGR $logMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');

        $visas = $entityMGR->findBy('App:CMVisaReq',[],['id'=>'DESC']);

        return $this->render('hrm/visa/VisaList.html.twig',
            [
                'visas'=>$visas
            ]);
    }
    /**
     * @Route("/hrm/visa/view/{id}/{msg}", name="HRMVisaView")
     */
    public function HRMVisaView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');

        $visa = $entityMGR->find('App:CMVisaReq',$id);
        $visa1 = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');

        $mlist = $entityMGR->find('App:CMList',$visa->getCMlist()->getId());
        $passengers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$mlist]);
        $alerts = [];

        $form = $this->createFormBuilder($visa)
            ->add('ARDes', TextareaType::class,['label'=>'توضیحات تکمیلی:','required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();
        $form1 = $this->createFormBuilder($visa1)
            ->add('ARDes', TextareaType::class,['label'=>'توضیحات تکمیلی:','required'=>false])
            ->add('submit1', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        $form1->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $visa->setADDateSubmit(time());
            $visa->setAccepter($userMGR->currentPosition());
            $acceptState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>1]);
            $visa->setVisaState($acceptState);
            $entityMGR->update($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'تایید','مدیریت تشریفات','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست ویزای شما توسط %s تایید شد.',$userMGR->currentPosition()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);
            $userMGR->sendSmsToUser($visa->getSubmitter(),$des);

            $des = sprintf('درخواست ویزا برای %s توسط %s تایید شد.',$visa->getSubmitter()->getPublicLabel(),$userMGR->currentPosition()->getPublicLabel());
            $url = $this->generateUrl('ceremonialOPTVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForGroup('CeremonailOPTDashboard','CEREMONIAL',$des,$url);
            $userMGR->sendSmsToGroup('CeremonailOPTDashboard','CEREMONIAL',$des);

            array_push($alerts,['type'=>'success','message'=>'درخواست ویزا با موفقیت تایید شد.']);
        }
        if ($form1->isSubmitted() && $form1->isValid()) {
            $visa1->setADDateSubmit(time());
            $visa1->setRejecter($userMGR->currentPosition());
            $acceptState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>-1]);
            $visa1->setVisaState($acceptState);
            $entityMGR->update($visa1);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'رد درخواست','مدیریت تشریفات','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست ویزای شما توسط %s رد شد.',$userMGR->currentPosition()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);
            $userMGR->sendSmsToUser($visa->getSubmitter(),$des);

            array_push($alerts,['type'=>'success','message'=>'درخواست ویزا با موفقیت رد شد.']);

        }
        return $this->render('hrm/visa/VisaView.html.twig', [
            'passengers' => $passengers,
            'visa'=>$visa,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERVISA'.$visa->getId()),
            'alerts'=>$alerts,
            'form'=>$form->createView(),
            'form1'=>$form1->createView()
        ]);
    }

    /**
     * @Route("/hrm/position/pasenger/new/{pid}", name="HRMPassengerNew")
     */
    public function HRMPassengerNew($pid,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');
        $position = $entityMGR->find('App:SysPosition',$pid);
        if(is_null($position))
            return $this->redirectToRoute('404');

        $passenger = new Entity\CMPassenger();
        $form = $this->createFormBuilder($passenger)
            ->add('pname', TextType::class,['label'=>'نام'])
            ->add('pfamily', TextType::class,['label'=>' نام خانوادگی'])
            ->add('pfather', TextType::class,['label'=>' نام پدر'])
            ->add('pbirthday',Type\JdateType::class,['label'=>'تاریخ تولد'])
            ->add('passportExpireDate',Type\JdateType::class,['label'=>'پایان اعتبار گذرنامه'])
            ->add('pshenasname', TextType::class,['label'=>' شماره شناسنامه'])
            ->add('tel1', TextType::class,['label'=>'شماره تماس1:','attr'=>['class'=>'tel']])
            ->add('tel2', TextType::class,['label'=>'شماره تماس2:','attr'=>['class'=>'tel']])
            ->add('adr', TextareaType::class,['label'=>'آدرس:'])
            ->add('pcodemeli', TextType::class,[
                'label'=>'کد ملی',
                'attr'     => array(
                    'class'  => 'codeMeli',
                ),
            ])
            ->add('passNo', TextType::class,['label'=>'Passport Number:'])
            ->add('lname', TextType::class,['label'=>'Name:'])
            ->add('lfamily', TextType::class,['label'=>'Family:'])
            ->add('lfather', TextType::class,['label'=>'Father Name:', 'required'=>false])
            ->add('ptype', EntityType::class, [
                'class'=>Entity\CMPassengerType::class,
                'choice_label'=>'typeName',
                'choice_value' => 'id',
                'label'=>'ارتباط مسافر'
            ])
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        $alert = [];
        if ($form->isSubmitted() && $form->isValid()) {
            if($passenger->getPtype()->getTypeName() == 'پرسنل شرکت') {
                if (is_null($entityMGR->findOneBy('App:SysUser', ['nationalCode' => $passenger->getPcodemeli(),'contractor'=> null]))) {
                    array_push($alert,['type' => 'danger', 'message' => 'این کد ملی در لیست پرسنل شرکت موجود نیست.']);
                }
            }
            elseif (!is_null($entityMGR->findOneBy('App:CMPassenger', ['pcodemeli' => $passenger->getPcodemeli()]))) {
                array_push($alert, ['type' => 'danger', 'message' => 'این کد ملی قبلا ثبت شده است.اگر اخیرا در ارتباط فرد با شرکت و یا شرکت‌های زیر مجموعه تغییری رخ داده است با مدیر سامانه تماس بگیرید.']);
            }
            if(count($alert ) == 0)
            {
                $passenger->setSubmitter($position);
                $entityMGR->insertEntity($passenger);
                $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','اطلاعات مسافر','CEREMONIAL',$request->getClientIp());
                return $this->redirectToRoute('HRMPositionFolder', ['id'=>$position->getId(),'msg' => 1]);
            }
        }

        return $this->render('hrm/newPassenger.html.twig', [
            'alerts'=>$alert,
            'form' => $form->createView(),
            'position'=>$position
        ]);
    }

    /**
     * @Route("/hrm/passenger/profile/view/{id}/{msg}", name="HRMPassengerProfile")
     */
    public function HRMPassengerProfile($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');

        $passenger = $entityMGR->findOneBy('App:CMPassenger',['pcodemeli'=>$id]);
        if(is_null($passenger))
            return $this->redirectToRoute('HRMEmployelist',['msg'=>1]);

        //get visas
        $visas = [];
        $lists = $entityMGR->findBy('App:CMListUser',['cmpassenger'=>$passenger]);
        foreach ($lists as $list){
            if($list->getCmlist()->getListLabel() == 'VisaRequest'){
                $visa = $entityMGR->findOneBy('App:CMVisaReq',['cmlist'=>$list->getCmlist()]);
                if(! is_null($visa)){
                    array_push($visas,$visa);
                }
            }
        }
        //get tickets
        $tickets = [];
        $lists = $entityMGR->findBy('App:CMListUser',['cmpassenger'=>$passenger]);
        foreach ($lists as $list){
            if($list->getCmlist()->getListLabel() == 'TicketRequest'){
                $ticket = $entityMGR->findOneBy('App:CMVisaReq',['cmlist'=>$list->getCmlist()]);
                if(! is_null($visa)){
                    array_push($tickets,$ticket);
                }
            }
        }

        $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'مشاهده','اطلاعات مسافر','CEREMONIAL',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'مدارک مسافر با موفقیت اضافه شد.']);
        $doc = new Entity\CMPassengerPersonalDoc();
        $doc->setPassenger($passenger);
        $form = $this->createFormBuilder($doc)
            ->add('docName', Type\FileboxType::class,['label'=>'فایل اسکن'])
            ->add('docType', EntityType::class, [
                'class'=>Entity\CMPassengerDocType::class,
                'choice_label'=>'tname',
                'choice_value' => 'id',
                'label'=>'نوع مدرک'
            ])
            ->add('submit', SubmitType::class,['label'=>'ذخیره'])
            ->getForm();

        $form->handleRequest($request);
        $file = $form->get('docName')->getData();
        $guid = $this->RandomString(32);
        if ($form->isSubmitted() && $form->isValid()) {
            if($file->getClientOriginalExtension() == 'pdf'  || $file->getClientOriginalExtension() == 'jpg' || $file->getClientOriginalExtension() == 'jpeg' || $file->getClientOriginalExtension() == 'png'){
                if($file->getSize() < 200971502){
                    $tempFileName = $guid . '.' . $file->getClientOriginalExtension();
                    $file->move(str_replace('src','public_html',dirname(__DIR__)) . '/files',$tempFileName );
                    $doc->setDocName($tempFileName);
                    $entityMGR->insertEntity($doc);
                    $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','اسکن اطلاعات مسافر','CEREMONIAL',$request->getClientIp());
                    array_push($alerts,['type'=>'success','message'=>'فایل مورد نظر با موفقیت ذخیره شد.']);
                }
                else{
                    array_push($alerts,['type'=>'danger','message'=>'فایل ارسال شده بسیار حجیم است.حداکثر حجم ارسال فایل 2 مگابایت می باشد.']);
                }
            }
            else{
                array_push($alerts, ['type'=>'danger','message'=>'نوع فایل وارد شده صحیح نیست.لطفا فایل ,png,pdf,jpeg  ارسال فرمایید.']);
            }
        }
        return $this->render('hrm/passengerViewProfile.html.twig', [
            'passenger' => $passenger,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERPASSENGER'.$passenger->getId()),
            'form'=>$form->createView(),
            'alerts'=>$alerts,
            'docs'=>$passenger->getCMPassengerPersonalDocs(),
            'visas'=>$visas,
            'tickets'=>$tickets,
            'letters'=> $entityMGR->findBy('App:HRMLetterOutCountry',['user'=>$entityMGR->findOneBy('App:SysUser',['nationalCode'=>$passenger->getPcodemeli()])]),
        ]);
    }

    /**
     * @Route("/hrm/list/reports", name="HRMListReports")
     */
    public function HRMListReports(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');



        return $this->render('hrm/reportList.html.twig', [

        ]);
    }

    /**
     * @Route("/hrm/list/rpt/{type}", name="HRMPersonsReport")
     */
    public function HRMPersonsReport($type = 1,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if (!$userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if (!$userMGR->hasPermission('HRMACCESS', 'HRM'))
            return $this->redirectToRoute('403');

        return $this->render('hrm/employes.html.twig', [
            'users' => $entityMGR->findBy('App:SysUser',['contractor'=>null])
        ]);
    }

    /**
     * @Route("/hrm/employe/search/{type}", name="HRMEMployeREport")
     */
    public function HRMEMployeREport($type = 0, Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');

        $users = [];
        $poUsers = $entityMGR->findAll('App:SysUser');
        foreach ($poUsers as $poUser){
            $uofs = $entityMGR->findBy('App:SysPosition',['userID'=>$poUser,'constractor'=>$type]);
            foreach ($uofs as $uof){
                if(array_search($poUser,$users) == false){
                    array_push($users,$poUser);
                }
            }
        }
        return $this->render('hrm/reportEploye.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/hrm/employe/conctractors/search", name="HRMConsctractorPersons")
     */
    public function HRMConsctractorPersons( Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');

        $passengers = [];
        $positions = $entityMGR->findBy('App:SysPosition',['constractor'=>1]);
        foreach ($positions as $position){
            $tps = $entityMGR->findBy('App:CMPassenger',['submitter'=>$position]);
            foreach ($tps as $tp){
                if(array_search($tp,$passengers) == false){
                    array_push($passengers,$tp);
                }
            }
        }
        return $this->render('hrm/reportConstractorEmployes.html.twig', [
            'users' => $passengers
        ]);
    }

    /**
     * @Route("/hrm/employe/doc/delete/{id}", name="HRMdocDelete")
     */
    public function HRMdocDelete($id, Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');
        $doc = $entityMGR->find('App:CMPassengerPersonalDoc',$id);
        if(! is_null($doc)){
            $userCode = $doc->getPassenger()->getPcodemeli();
            $entityMGR->remove('App:CMPassengerPersonalDoc',$id);
            return $this->redirectToRoute('HRMPassengerProfile',['id'=>$userCode,'msg'=>2]);
        }
        return $this->redirectToRoute('404');
    }

    /**
     * @Route("/hrm/employe/passports/list/{type}", name="HRMPassportList")
     */
    public function HRMPassportList($type = 0, Service\Jdate $jdate,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMACCESS','HRM'))
            return $this->redirectToRoute('403');

        $passengers = [];
        $positions = $entityMGR->findBy('App:SysPosition',['constractor'=>$type]);
        //constractor
        if($type ==1){
            foreach ($positions as $position){
                $tps = $entityMGR->findBy('App:CMPassenger',['submitter'=>$position]);
                foreach ($tps as $tp){
                    if(array_search($tp,$passengers) == false){
                        array_push($passengers,$tp);
                    }
                }
            }
        }
        else{
            foreach ($positions as $position){
                $ptype = $entityMGR->findOneBy('App:CMPassengerType',['typeName'=>'پرسنل شرکت']);
                $tps = $entityMGR->findBy('App:CMPassenger',['submitter'=>$position,'ptype'=>$ptype]);
                foreach ($tps as $tp){
                    if(array_search($tp,$passengers) == false){
                        array_push($passengers,$tp);
                    }
                }
            }
        }
        return $this->render('hrm/reportPassports.html.twig', [
            'users' => $passengers,
            'timeNow' => $jdate->jdate('Ymd',time())
        ]);
    }

    /**
     * @Route("/hrm/visa/report/list/{countryID}", name="HRMVisaReport")
     */
    public function HRMVisaReport($countryID = 0, Service\Jdate $jdate,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if (!$userMGR->hasPermission('HRMACCESS', 'HRM'))
            return $this->redirectToRoute('403');
        $country = $entityMGR->find('App:CMVisaCountry',$countryID);
        if(is_null($country))
            return $this->redirectToRoute('404');
        $visas = $entityMGR->findBy('App:CMVisaLog',['country'=>$country],['id'=>'DESC']);
        echo 100;
        return $this->render('hrm/reportVisa.html.twig', [
            'timeNow' => $jdate->jdate('Ymd',time()),
            'visas' => $visas
        ]);


    }

}
