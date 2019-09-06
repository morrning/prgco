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

class CeremonialController extends AbstractController
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
     * @Route("/ceremonial/req/dashboard", name="ceremonialREQDashboard")
     */
    public function ceremonialREQDashboard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('FRE56','مشاهده','داشبورد سامانه درخواست تشریفات','CEREMONIAL',$request->getClientIp());
        return $this->render('ceremonial/REQDashboard.html.twig', [
            'controller_name' => 'CeremonialController',
        ]);
    }

    /**
     * @Route("/ceremonial/req/acc/balance/{msg}", name="ceremonialACCBalance")
     */
    public function ceremonialACCBalance($msg=0,Request $request,Service\ACC $ACC,Service\LogMGR $logMGR, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $moneyLabels=[];
        $moneyTypes = $entityMGR->findAll('App:ACCMoney');
        $moneyTotal = [];

        if($ACC->hasAccount($userMGR->currentUser()))
            $account = $ACC->getAccountByUser($userMGR->currentUser());
        else
            $account = $ACC->addAccount($userMGR->currentUser()->getFullname(),$userMGR->currentUser());
        foreach ($moneyTypes as $moneyType)
        {
            array_push($moneyLabels, $moneyType->getMoneyName());
            $docs = $entityMGR->findBy('App:ACCdoc',['account'=>$account,'Money'=>$moneyType]);
            $total = 0;
            foreach ($docs as $doc){
                $total = $total + $doc->getTotalValue();
            }
            array_push($moneyTotal,$total);
        }
        $account = $entityMGR->findOneBy('App:ACCaccount',['user'=>$userMGR->currentUser()]);
        $docs = $account->getACCdocs();


        return $this->render('ceremonial/REQACCdashboard.html.twig', [
            'moneys' => $moneyTypes,
            'moneyLabels'=>$moneyLabels,
            'moneyTotals'=>$moneyTotal,
            'docs'=>$docs
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasengers/{msg}", name="ceremonialREQpasengers")
     */
    public function ceremonialREQpasengers($msg=0,Request $request,Service\LogMGR $logMGR, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'اطلاعات مسافر اضافه شد.']);
        elseif($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'اطلاعات مسافر ویرایش شد.']);
        elseif($msg == 3)
            array_push($alerts,['type'=>'success','message'=>'اطلاعات مسافر حذف شد.']);

        $logMGR->addEvent('FRE56','مشاهده','لیست مسافران','CEREMONIAL',$request->getClientIp());

        return $this->render('ceremonial/REQPassengers.html.twig', [
            'passengers' => $userMGR->currentPosition()->getcMPassengers(),
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasenger/delete/{id}", name="ceremonialREQpasengerDelete")
     */
    public function ceremonialREQpasengerDelete($id,Request $request,Service\LogMGR $logMGR, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if($userMGR->currentPosition()->getId() != $passenger->getSubmitter()->getId())
            return $this->redirectToRoute('403');
        $logMGR->addEvent('FRE56','حذف','مدیریت مسافران','CEREMONIAL',$request->getClientIp());
        $entityMGR->remove('App:CMPassenger',$id);
        return $this->redirectToRoute('ceremonialREQpasengers',['msg'=>3]);
    }

    /**
     * @Route("/ceremonial/req/pasenger/view/{id}/{msg}", name="ceremonialREQpasengerView")
     */
    public function ceremonialREQpasengerView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($passenger))
            return $this->redirectToRoute('404');
        elseif ($passenger->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');
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
                if($file->getSize() < 2097152){
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
        return $this->render('ceremonial/viewPassengerInfo.html.twig', [
            'passenger' => $passenger,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERPASSENGER'.$passenger->getId()),
            'form'=>$form->createView(),
            'alerts'=>$alerts,
            'docs'=>$passenger->getCMPassengerPersonalDocs()
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasenger/new", name="ceremonialREQpasengerNew")
     */
    public function ceremonialREQpasengerNew(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $passenger = new Entity\CMPassenger();
        $form = $this->createFormBuilder($passenger)
            ->add('pname', TextType::class,['label'=>'نام'])
            ->add('pfamily', TextType::class,['label'=>' نام خانوادگی'])
            ->add('pfather', TextType::class,['label'=>' نام پدر'])
            ->add('pbirthday',Type\JdateType::class,['label'=>'تاریخ تولد'])
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
            ->add('visaNo', TextType::class,['label'=>'Visa Number:'])
            ->add('passNo', TextType::class,['label'=>'Passport Number:'])
            ->add('lname', TextType::class,['label'=>'Name:'])
            ->add('lfamily', TextType::class,['label'=>'Family:'])
            ->add('lfather', TextType::class,['label'=>'Father Name:', 'required'=>false])
            ->add('ptype', EntityType::class, [
                'class'=>Entity\CMPassengerType::class,
                'choice_label'=>'typeName',
                'choice_value' => 'id',
                'label'=>'ارتباط مسافر با شما؟'
            ])
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            if (is_null($entityMGR->findOneBy('App:CMPassenger', ['pcodemeli' => $passenger->getPcodemeli()]))) {
                $passenger->setSubmitter($userMGR->currentPosition());
                $entityMGR->insertEntity($passenger);
                $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','اطلاعات مسافر','CEREMONIAL',$request->getClientIp());
                return $this->redirectToRoute('ceremonialREQpasengers', ['msg' => 1]);
            }
            $alert = [['type' => 'danger', 'message' => 'این کد ملی قبلا ثبت شده است.']];
        }
        return $this->render('ceremonial/newPasenger.html.twig', [
            'alerts'=>$alert,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasenger/edit/{id}", name="ceremonialREQpasengerEdit")
     */
    public function ceremonialREQpasengerEdit($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($passenger))
            return $this->redirectToRoute('404');
        elseif ($passenger->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');

        $form = $this->createFormBuilder($passenger)
            ->add('pname', TextType::class,['label'=>'نام'])
            ->add('pfamily', TextType::class,['label'=>' نام خانوادگی'])
            ->add('pfather', TextType::class,['label'=>' نام پدر'])
            ->add('pbirthday',Type\JdateType::class,['label'=>'تاریخ تولد'])
            ->add('visaNo', TextType::class,['label'=>'Visa Number:'])
            ->add('passNo', TextType::class,['label'=>'Passport Number:'])
            ->add('lname', TextType::class,['label'=>'Name:'])
            ->add('ptype', EntityType::class, [
                'class'=>Entity\CMPassengerType::class,
                'choice_label'=>'typeName',
                'choice_value' => 'id',
                'label'=>'ارتباط مسافر با شما؟',
                'data'=>$passenger->getPtype(),
            ])
            ->add('tel1', TextType::class,['label'=>'شماره تماس1:','attr'=>['class'=>'tel']])
            ->add('tel2', TextType::class,['label'=>'شماره تماس2:','attr'=>['class'=>'tel']])
            ->add('adr', TextareaType::class,['label'=>'آدرس:'])
            ->add('lfamily', TextType::class,['label'=>'Family:'])
            ->add('lfather', TextType::class,['label'=>'Father Name:', 'required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $passenger->setSubmitter($userMGR->currentPosition());
            $entityMGR->update($passenger);
            $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'ویرایش','اطلاعات مسافر','CEREMONIAL',$request->getClientIp());
            return $this->redirectToRoute('ceremonialREQpasengers',['msg'=>2]);
        }
        return $this->render('ceremonial/editPassenger.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ceremonial/req/air/ticket/new/{id}", name="ceremonialREQAIRpaneNew")
     */
    public function ceremonialREQAIRpaneNew($id,Request $request,Service\Jdate $jdate,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($passenger))
            return $this->redirectToRoute('404');
        elseif ($passenger->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');

        $ticket = new Entity\CMAirTicket();
        $form = $this->createFormBuilder($ticket)
            ->add('suggestTime', EntityType::class, [
                'class'=>Entity\CMdaytime::class,
                'choice_label'=>'label',
                'choice_value' => 'id',
                'label'=>'ساعت پیشنهادی:'
            ])
            ->add('source', EntityType::class, [
                'class'=>Entity\CMCities::class,
                'choice_label'=>'cname',
                'choice_value' => 'id',
                'label'=>'مبدا حرکت:'
            ])
            ->add('destination', EntityType::class, [
                'class'=>Entity\CMCities::class,
                'choice_label'=>'cname',
                'choice_value' => 'id',
                'label'=>'مقصد حرکت:'
            ])
            ->add('dateSuggest',Type\JdateType::class,['label'=>'تاریخ مسافرت:','data'=>$jdate->GetTodayDate()])
            ->add('des', TextareaType::class,['label'=>'علت سفر:','required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت درخواست'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($ticket->getSource() == $ticket->getDestination()){
                array_push($alerts,['type'=>'warning','message'=>'مبدا و مقصد سفر نمیتواند یکسان باشد.']);
            }
            else{
                $ticket->setPassengerID($passenger);
                $ticket->setDateSubmit(time());
                $ticket->setArea($userMGR->currentPosition()->getDefaultArea());
                $ticket->setSubmitter($userMGR->currentPosition());
                $ticket->setTicketState($entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>0]));
                $entityMGR->insertEntity($ticket);
                $logMGR->addEvent('CERTICKET'.$ticket->getId(),'ایجاد','درخواست بلیط','CEREMONIAL',$request->getClientIp());
                $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','درخواست بلیط هواپیما','CEREMONIAL',$request->getClientIp());
                $des = sprintf('درخواست بلیط هواپیما توسط %s ثبت شد.',$ticket->getSubmitter()->getPublicLabel());
                $url = $this->generateUrl('ceremonialDOINGTicketView',['id'=>$ticket->getId()]);
                $userMGR->addNotificationForGroup('CeremonailMNGDashboard','CEREMONIAL',$des,$url);
                return $this->redirectToRoute('ceremonialREQTicketView',['id'=>$ticket->getId(),'msg'=>1]);
            }
        }
        return $this->render('ceremonial/reqAIRpane.html.twig',[
            'passenger'=>$passenger,
            'form'=>$form->createView(),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/ceremonial/req/air/ticket/list", name="ceremonialREQAIRpaneList")
     */
    public function ceremonialREQAIRpaneList(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $tickets = $entityMGR->findBy('App:CMAirTicket',['submitter'=>$userMGR->currentPosition()]);

        return $this->render('ceremonial/REQAirTicketsList.html.twig',
            [
                'tickets'=>$tickets
            ]);
    }

    /**
     * @Route("/ceremonial/req/ticket/view/{id}/{msg}", name="ceremonialREQTicketView")
     */
    public function ceremonialREQTicketView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $ticket = $entityMGR->find('App:CMAirTicket',$id);
        if(is_null($ticket))
            return $this->redirectToRoute('404');
        elseif ($ticket->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$ticket->getPassengerID()->getId());
        $logMGR->addEvent('CERTICKET'.$ticket->getId(),'مشاهده','اطلاعات درخواست بلیط','CEREMONIAL',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'درخواست بلیط با موفقیت ثبت شد.']);

        return $this->render('ceremonial/REQTicketView.html.twig', [
            'passenger' => $passenger,
            'ticket'=>$ticket,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERTICKET'.$ticket->getId()),
            'alerts'=>$alerts
        ]);
    }

    //--------------------------------------------MANAGER PART ----------------------------------------

    /**
     * @Route("/ceremonial/doing/dashboard", name="ceremonialDOINGDashboard")
     */
    public function ceremonialDOINGDashboard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('FRE56','مشاهده','داشبورد سامانه درخواست تشریفات','CEREMONIAL',$request->getClientIp());

        return $this->render('ceremonial/DOINGDashboard.html.twig', [
            'controller_name' => 'CeremonialController',
        ]);
    }

    /**
     * @Route("/ceremonial/mng/visa/list/{type}", name="ceremonialDOINGVisaList")
     */
    public function ceremonialDOINGVisaList($type = 'all',Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');
        if($type == 'all'){
            $visas = $entityMGR->findAll('App:CMVisaReq');
            $typeName = 'آرشیو تمام درخواست‌ها';
        }
        elseif ($type == 'wfd'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>3]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state]);
            $typeName = 'پاسپورت‌های در انتظار تایید';
        }
        elseif ($type == 'accepted'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>4]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state]);
            $typeName = 'پاسپورت‌های تایید شده';
        }
        else
            return $this->redirectToRoute('404');

        return $this->render('ceremonial/DOINGVisaList.html.twig',
            [
                'visas'=>$visas,
                'typeName'=>$typeName
            ]);
    }

    /**
     * @Route("/ceremonial/doing/visa/view/{id}/{msg}", name="ceremonialDOINGVisaView")
     */
    public function ceremonialDOINGVisaView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');

        $visa = $entityMGR->find('App:CMVisaReq',$id);
        $visa1 = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');
        $passenger = $entityMGR->find('App:CMPassenger',$visa->getPassenger()->getId());
        $logMGR->addEvent('CERVISA'.$visa->getId(),'مشاهده','اطلاعات درخواست ویزا','CEREMONIAL',$request->getClientIp());
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
            $visa->setARSubmitDate(time());
            $visa->setAccepter($userMGR->currentPosition());
            $acceptState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>4]);
            $visa->setVisaState($acceptState);
            $entityMGR->update($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'تایید','ایمنی و اقدامات تامینی','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست ویزای شما توسط %s تایید شد.',$visa->getHseAR()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);

            $des = 'درخواست ویزا توسط مدیریت تایید شد.';
            $url = $this->generateUrl('ceremonialOPTVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForGroup('CeremonailOPTDashboard','CEREMONIAL',$des,$url);
            array_push($alerts,['type'=>'success','message'=>'درخواست ویزا با موفقیت تایید شد.']);
        }
        if ($form1->isSubmitted() && $form1->isValid()) {
            $visa1->setARSubmitDate(time());
            $visa1->setRejecter($userMGR->currentPosition());
            $acceptState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>-2]);
            $visa1->setVisaState($acceptState);
            $entityMGR->update($visa1);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'تایید','ایمنی و اقدامات تامینی','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست ویزای شما توسط %s رد شد.',$visa->getHseAR()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);

            array_push($alerts,['type'=>'success','message'=>'درخواست ویزا با موفقیت رد شد.']);

        }
        return $this->render('ceremonial/DoingVisaView.html.twig', [
            'passenger' => $passenger,
            'visa'=>$visa,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERVISA'.$visa->getId()),
            'alerts'=>$alerts,
            'form'=>$form->createView(),
            'form1'=>$form1->createView()
        ]);
    }


    /**
     * @Route("/ceremonial/doing/air/ticket/list/{type}", name="ceremonialDOINGAIRpaneList")
     */
    public function ceremonialDOINGAIRpaneList($type='acp',Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');

        $tickets = $entityMGR->findBy('App:CMAirTicket',[],['ticketState'=>'ASC']);
        if($type == 'acp'){
            $ticketState = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>0]);
            $tickets=$entityMGR->findBy('App:CMAirTicket',['ticketState'=>$ticketState],['ticketState'=>'ASC']);
        }
        return $this->render('ceremonial/DOINGAirTicketList.html.twig',
            [
                'tickets'=>$tickets
            ]);
    }

    /**
     * @Route("/ceremonial/doing/ticket/view/{id}/{msg}", name="ceremonialDOINGTicketView")
     */
    public function ceremonialDOINGTicketView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');
        $ticket = $entityMGR->find('App:CMAirTicket',$id);
        if(is_null($ticket))
            return $this->redirectToRoute('404');

        $passenger = $entityMGR->find('App:CMPassenger',$ticket->getPassengerID()->getId());
        $logMGR->addEvent('CERTICKET'.$ticket->getId(),'مشاهده','اطلاعات درخواست بلیط','CEREMONIAL',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'درخواست با موفقیت رد شد.']);
        if($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'درخواست با موفقیت تایید شد.']);

        $form = $this->createFormBuilder($ticket)
            ->add('acceptIF', EntityType::class, [
                'class'=>Entity\CMacceptIF::class,
                'choice_label'=>'ifName',
                'choice_value' => 'id',
                'label'=>'مرکز هزینه این بلیط:'
            ])
            ->add('acceptDes', TextareaType::class,['label'=>'توضیحات تکمیلی:','required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();
        $form1 = $this->createFormBuilder($ticket)
            ->add('rejectDes', TextareaType::class,['label'=>'توضیحات تکمیلی:','required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        $form1->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setAccepter($userMGR->currentPosition());
            $ticket->setAcceptDate(time());
            $acceptState = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>2]);
            $ticket->setTicketState($acceptState);
            $logMGR->addEvent('CERTICKET'.$ticket->getId(),'تایید درخواست','درخواست بلیط','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست بلیط شما توسط %s تایید شد.',$ticket->getAccepter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQTicketView',['id'=>$ticket->getId()]);
            $userMGR->addNotificationForUser($ticket->getSubmitter(),$des,$url);
            $des = sprintf('درخواست بلیط توسط %s تایید شد.',$ticket->getAccepter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialOPTTicketView',['id'=>$ticket->getId()]);
            $userMGR->addNotificationForGroup('CeremonailOPTDashboard','CEREMONIAL',$des,$url);
            return $this->redirectToRoute('ceremonialDOINGTicketView',['id'=>$ticket->getId(),'msg'=>2]);
        }

        if ($form1->isSubmitted() && $form1->isValid()) {
            $ticket->setRejecter($userMGR->currentPosition());
            $ticket->setRejectDate(time());
            $rejectstate = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>1]);
            $ticket->setTicketState($rejectstate);
            $entityMGR->update($ticket);
            $logMGR->addEvent('CERTICKET'.$ticket->getId(),'رد درخواست','درخواست بلیط','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست بلیط شما توسط %s رد شد.',$ticket->getRejecter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQTicketView',['id'=>$ticket->getId()]);
            $userMGR->addNotificationForUser($ticket->getSubmitter(),$des,$url);
            return $this->redirectToRoute('ceremonialDOINGTicketView',['id'=>$ticket->getId(),'msg'=>1]);
        }

        return $this->render('ceremonial/DOINGTicketView.html.twig', [
            'travels'=>$entityMGR->findBy('App:CMAirTicket',['passengerID'=>$passenger]),
            'passenger' => $passenger,
            'ticket'=>$ticket,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERTICKET'.$ticket->getId()),
            'alerts'=>$alerts,
            'form'=>$form->createView(),
            'form1'=>$form1->createView()
        ]);
    }

    //--------------------------------------------OPERATOR PART ----------------------------------------
    /**
     * @Route("/ceremonial/opt/dashboard", name="ceremonialOPTDashboard")
     */
    public function ceremonialOPTDashboard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('FRE56','مشاهده','داشبورد سامانه درخواست تشریفات','CEREMONIAL',$request->getClientIp());

        return $this->render('ceremonial/OPTDashboard.html.twig', [
            'controller_name' => 'CeremonialController',
        ]);
    }

    /**
     * @Route("/ceremonial/opt/air/ticket/list/{type}", name="ceremonialOPTAIRpaneList")
     */
    public function ceremonialOPTAIRpaneList($type,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');
        if($type == 'acp'){
            $state = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>'2']);
            $tickets = $entityMGR->findBy('App:CMAirTicket',['ticketState'=>$state],['dateSubmit'=>'DESC']);
        }
        elseif($type == 'all'){
            $state = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>'2']);
            $tickets = $entityMGR->findBy('App:CMAirTicket',[],['dateSubmit'=>'DESC']);
        }

        return $this->render('ceremonial/OPTAirTicketList.html.twig',
            [
                'tickets'=>$tickets,
                'type'=>$type
            ]);
    }

    /**
     * @Route("/ceremonial/opt/ticket/view/{id}/{msg}", name="ceremonialOPTTicketView")
     */
    public function ceremonialOPTTicketView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,Service\ACC $ACC)
    {
        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');
        $ticket = $entityMGR->find('App:CMAirTicket',$id);
        if(is_null($ticket))
            return $this->redirectToRoute('404');

        $passenger = $entityMGR->find('App:CMPassenger',$ticket->getPassengerID()->getId());
        $logMGR->addEvent('CERTICKET'.$ticket->getId(),'مشاهده','اطلاعات درخواست بلیط','CEREMONIAL',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'درخواست با موفقیت رد شد.']);
        if($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'درخواست با موفقیت تایید شد.']);

        $default =['message'=>'test'];
        $form = $this->createFormBuilder($default)
            ->add('flyAirway', EntityType::class, [
                'class'=>Entity\CMAirway::class,
                'choice_label'=>'airwayName',
                'choice_value' => 'id',
                'label'=>'شرکت هواپیمایی'
            ])
            ->add('moneyType', EntityType::class, [
                'class'=>Entity\ACCMoney::class,
                'choice_label'=>'moneyName',
                'choice_value' => 'id',
                'label'=>'واحد پولی'
            ])
            ->add('flyNumber', TextType::class,['label'=>'شماره پرواز'])
            ->add('moneyValue', Type\NumbermaskType::class,['label'=>'مقدار هزینه','attr'=>['class'=>'MoneyInput']])
            ->add('flyDate',Type\JdateType::class,['label'=>'تاریخ پرواز','data'=>$ticket->getDateSuggest()])
            ->add('flyTime', TimeType::class, [
                'label'=>'ساعت پرواز',
                'input'  => 'string',
                'widget' => 'choice',
                'with_seconds'=>false
            ])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setFlyAirway($form->get('flyAirway')->getData());
            $ticket->setMoneyType($form->get('moneyType')->getData());
            $ticket->setFlyNumber($form->get('flyNumber')->getData());
            $ticket->setMoneyValue($form->get('moneyValue')->getData());
            $ticket->setFlyDate($form->get('flyDate')->getData());
            $ticket->setFlyTime($form->get('flyTime')->getData());
            $ticket->setMoneyValue(str_replace(',','',$ticket->getMoneyValue()));
            $ticket->setBuyer($userMGR->currentPosition());
            $ticket->setBuyDate(time());
            $acceptState = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>3]);
            $ticket->setTicketState($acceptState);

            //accounting process
            if($ticket->getAcceptIF()->getIfCode()==1)
                $account = $ACC->getAccountByAccountNo(1);
            else{
                if($ACC->hasAccount($ticket->getSubmitter()->getUserID()))
                    $account = $ACC->getAccountByUser($ticket->getSubmitter()->getUserID());
                else
                    $account = $ACC->addAccount($ticket->getSubmitter()->getUserID()->getFullname(),$ticket->getSubmitter()->getUserID());
            }

            $ic = $ACC->getICCenter(10002);
            $document = $ACC->addDocument('ایاب و ذهاب',$ticket->getMoneyType(),$userMGR->currentPosition(),$ic,$account);
            $ACC->addDocumentItem($document,$ticket->getMoneyValue());

            $logMGR->addEvent('CERTICKET'.$ticket->getId(),'خرید بلیط','درخواست بلیط','CEREMONIAL',$request->getClientIp());
            $des = sprintf(' بلیط شما توسط %s خریداری شد.',$ticket->getBuyer()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQTicketView',['id'=>$ticket->getId()]);
            $userMGR->addNotificationForUser($ticket->getSubmitter(),$des,$url);
            array_push($alerts,['type'=>'success','message'=>'اطلاعات بلیط با موفقیت ثبت شد.']);
        }
        $ticket1 = $entityMGR->find('App:CMAirTicket',$id);
        $form1 = $this->createFormBuilder($ticket1)
            ->add('fileID', Type\FileboxType::class,['label'=>'فایل اسکن','data_class'=>null])
            ->add('submit', SubmitType::class,['label'=>'ذخیره'])
            ->getForm();

        $form1->handleRequest($request);
        $file = $form1->get('fileID')->getData();
        $guid = $this->RandomString(32);
        if ($form1->isSubmitted() && $form1->isValid()) {
            if($file->getClientOriginalExtension() == 'pdf'  || $file->getClientOriginalExtension() == 'jpg' || $file->getClientOriginalExtension() == 'jpeg' || $file->getClientOriginalExtension() == 'png'){
                if($file->getSize() < 4097152){
                    $tempFileName = $guid . '.' . $file->getClientOriginalExtension();
                    $file->move(str_replace('src','public_html',dirname(__DIR__)) . '/files',$tempFileName );
                    $ticket1->setFileID($tempFileName);
                    $entityMGR->update($ticket1);
                    $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','بارگزاری بلیط هواپیما','CEREMONIAL',$request->getClientIp());
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
        return $this->render('ceremonial/OPTTicketView.html.twig', [
            'travels'=>$entityMGR->findBy('App:CMAirTicket',['passengerID'=>$passenger]),
            'passenger' => $passenger,
            'ticket'=>$ticket,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERTICKET'.$ticket->getId()),
            'alerts'=>$alerts,
            'form'=>$form->createView(),
            'form1'=>$form1->createView()
        ]);
    }


    //--------------------------------------------- passport proccess ---------------------------------->
    /**
     * @Route("/ceremonial/req/passport/new/{id}", name="ceremonialPassportNew")
     */
    public function ceremonialPassportNew($id,Request $request,Service\Jdate $jdate,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($passenger))
            return $this->redirectToRoute('404');
        elseif ($passenger->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');

        $visa = new Entity\CMVisaReq();
        $form = $this->createFormBuilder($visa)
            ->add('countryDes', EntityType::class, [
                'class'=>Entity\CMVisaCountry::class,
                'choice_label'=>'countryName',
                'choice_value' => 'id',
                'label'=>'مقصد مسافرت:'
            ])
            ->add('WaySendToCo', EntityType::class, [
                'class'=>Entity\CMVisaSendWay::class,
                'choice_label'=>'WayName',
                'choice_value' => 'id',
                'label'=>'روش ارسال ویزا:'
            ])
            ->add('dateSendToCo',Type\JdateType::class,['label'=>'تاریخ مسافرت:','data'=>$jdate->GetTodayDate()])
            ->add('des', TextareaType::class,['label'=>'علت سفر:','required'=>false])

            ->add('submit', SubmitType::class,['label'=>'ثبت درخواست'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $visa->setPassenger($passenger);
            $visa->setDateSubmit(time());
            $visa->setSubmitter($userMGR->currentPosition());
            $visa->setArea($userMGR->currentPosition()->getDefaultArea());
            $visa->setVisaState($entityMGR->findOneBy('App:CMVisaState',['StateCode'=>1]));

            $entityMGR->insertEntity($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'ایجاد','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست ویزا توسط %s ثبت شد.',$visa->getSubmitter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialOPTVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForGroup('CeremonailOPTDashboard','CEREMONIAL',$des,$url);
            return $this->redirectToRoute('ceremonialREQVisaView',['id'=>$visa->getId(),'msg'=>1]);
        }
        return $this->render('ceremonial/REQVisaNew.html.twig',[
            'passenger'=>$passenger,
            'form'=>$form->createView(),
            'alerts'=>$alerts
        ]);
    }
    /**
     * @Route("/ceremonial/req/Visa/view/{id}/{msg}", name="ceremonialREQVisaView")
     */
    public function ceremonialREQVisaView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,Service\ACC $ACC)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $visa = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');
        elseif ($visa->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$visa->getPassenger()->getId());
        $logMGR->addEvent('CERVISA'.$visa->getId(),'مشاهده','اطلاعات درخواست ویزا','CEREMONIAL',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'درخواست ویزا با موفقیت ثبت شد.']);

        return $this->render('ceremonial/REQVisaView.html.twig', [
            'passenger' => $passenger,
            'visa'=>$visa,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERVISA'.$visa->getId()),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/ceremonial/req/visa/list", name="ceremonialREQVisaList")
     */
    public function ceremonialREQVisaList(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $visas = $entityMGR->findBy('App:CMVisaReq',['submitter'=>$userMGR->currentPosition()]);

        return $this->render('ceremonial/REQVisaList.html.twig',
            [
                'visas'=>$visas
            ]);
    }

    //----------------------------------------- operator part ----------------------------------------
    /**
     * @Route("/ceremonial/opt/visa/list/{type}", name="ceremonialOPTVisaList")
     */
    public function ceremonialOPTVisaList($type = 'all',Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');
        if($type == 'all'){
            $visas = $entityMGR->findAll('App:CMVisaReq');
            $typeName = 'آرشیو تمام درخواست‌ها';
        }
        elseif ($type == 'wfd'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>1]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state]);
            $typeName = 'پاسپورت‌های منتظر وصول';
        }
        elseif ($type == 'accepted'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>4]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state]);
            $typeName = 'پاسپورت‌های تایید شده';
        }
        else
            return $this->redirectToRoute('404');

        return $this->render('ceremonial/OPTVisaList.html.twig',
            [
                'visas'=>$visas,
                'typeName'=>$typeName
            ]);
    }


    /**
     * @Route("/ceremonial/opt/visa/view/{id}/{msg}", name="ceremonialOPTVisaView")
     */
    public function ceremonialOPTVisaView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {

        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');
        $visa = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');
        $passenger = $entityMGR->find('App:CMPassenger',$visa->getPassenger()->getId());
        $logMGR->addEvent('CERVISA'.$visa->getId(),'مشاهده','اطلاعات درخواست ویزا','CEREMONIAL',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'اعلام وصول پاسپورت با موفقیت ثبت شد.']);

        return $this->render('ceremonial/OPTVisaView.html.twig', [
            'passenger' => $passenger,
            'visa'=>$visa,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERVISA'.$visa->getId()),
            'alerts'=>$alerts
        ]);

    }

    /**
     * @Route("/ceremonial/opt/visa/deliver/{id}", name="ceremonialOPTVisaDeliver")
     */
    public function ceremonialOPTVisaDeliver($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');

        $visa = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');
        $passenger = $visa->getPassenger();
        $visaState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>2]);
        $visa->setVisaState($visaState);
        $visa->setDateReciveToCo(time());
        $visa->setReciver($userMGR->currentPosition());
        $entityMGR->update($visa);

        $logMGR->addEvent('CERVISA'.$visa->getId(),'اعلام وصول','درخواست ویزا','CEREMONIAL',$request->getClientIp());
        $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','درخواست ویزا','CEREMONIAL',$request->getClientIp());
        $des = sprintf('درخواست ویزا توسط %s اعلام وصول شد.',$userMGR->currentPosition()->getPublicLabel());
        $url = $this->generateUrl('HSSECeremonialView',['id'=>$visa->getId()]);
        $userMGR->addNotificationForGroup('HSSETOTAL','HSSE',$des,$url);
        $des = sprintf('درخواست ویزا %s اعلام وصول شد.',$visa->getSubmitter()->getPublicLabel());
        $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
        $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);

        return $this->redirectToRoute('ceremonialOPTVisaView',['id'=>$visa->getId(),'msg'=>1]);
    }
}
