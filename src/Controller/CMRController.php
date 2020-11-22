<?php

namespace App\Controller;

use http\Client;
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

class CMRController extends AbstractController
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
        if(!$userMGR->isLogedIn())      return $this->redirectToRoute('userLogin');
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('FRE56','مشاهده','داشبورد سامانه درخواست تشریفات','CEREMONIAL',$request->getClientIp());
        return $this->render('cmr/passenger/REQDashboard.html.twig', [
            'controller_name' => 'CeremonialController',
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasengers/{msg}", name="ceremonialREQpasengers")
     */
    public function ceremonialREQpasengers($msg=0,Request $request,Service\LogMGR $logMGR, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(!$userMGR->isLogedIn())      return $this->redirectToRoute('userLogin');

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

        return $this->render('cmr/passenger/REQPassengers.html.twig', [
            'passengers' => $userMGR->currentPosition()->getcMPassengers(),
            'alerts' => $alerts,
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
            $alert = [['type' => 'danger', 'message' => 'این کد ملی قبلا ثبت شده است.اگر اخیرا در ارتباط شما با شرکت و یا شرکت‌های زیر مجموعه تغییری رخ داده است با مدیر سامانه تماس بگیرید.']];
        }
        return $this->render('cmr/passenger/newPasenger.html.twig', [
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
        return $this->render('cmr/passenger/editPassenger.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/ceremonial/req/pasenger/view/{id}/{msg}", name="ceremonialREQpasengerView")
     */
    public function ceremonialREQpasengerView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(!$userMGR->isLogedIn())      return $this->redirectToRoute('userLogin');

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
        return $this->render('cmr/passenger/viewPassengerInfo.html.twig', [
            'passenger' => $passenger,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERPASSENGER'.$passenger->getId()),
            'form'=>$form->createView(),
            'alerts'=>$alerts,
            'docs'=>$passenger->getCMPassengerPersonalDocs()
        ]);
    }

    /**
     * @Route("/ceremonial/request/visa/{ids}/", name="ceremonialREQVisa", options={"expose" = true} ,requirements={"ids"=".+"})
     */
    public function ceremonialREQVisa($ids,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(!$userMGR->isLogedIn())      return $this->redirectToRoute('userLogin');

        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $idsArray = explode(',',$ids);
        $mlist = new Entity\CMList();
        $mlist->setSubmitter($userMGR->currentPosition());
        $mlist->setDes('Visa Request');
        $mlist->setListLabel('VisaRequest');
        $entityMGR->insertEntity($mlist);

        foreach ($idsArray as $id){
            $passenger = $entityMGR->find('App:CMPassenger',$id);
            if(! is_null($passenger)){
                if($passenger->getSubmitter() == $userMGR->currentPosition()){
                    $listUser = new Entity\CMListUser();
                    $listUser->setCmlist($mlist);
                    $listUser->setCmpassenger($passenger);
                    $entityMGR->insertEntity($listUser);
                }
            }
        }
        if(count($idsArray) != 0)
            return new Response($mlist->getId());
        return new Response('error');

    }

    /**
     * @Route("/ceremonial/req/new/visa/{id}", name="ceremonialREQCompleteVisaRequest", options={"expose" = true})
     */
    public function ceremonialREQCompleteVisaRequest($id,Request $request,Service\Jdate $jdate,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $mlist = $entityMGR->find('App:CMList',$id);
        if(is_null($mlist))
            return $this->redirectToRoute('404');

        elseif ($mlist->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
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
                'label'=>'روش ارسال گذرنامه:'
            ])
            ->add('des', TextareaType::class,['label'=>'علت سفر:','required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت درخواست'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $visa->setCmlist($mlist);
            $visa->setDateSubmit(time());
            $visa->setVisaState($entityMGR->findOneBy('App:CMVisaState',['StateCode'=>0]));
            $visa->setSubmitter($userMGR->currentPosition());
            $visa->setArea($userMGR->currentPosition()->getDefaultArea());
            $entityMGR->insertEntity($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'ایجاد','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $logMGR->addEvent('CERPASSENGER'.$mlist->getId(),'افزودن','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست ویزا توسط %s ثبت شد.',$visa->getSubmitter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialDOINGVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForGroup('CeremonailMNGDashboard','CEREMONIAL',$des,$url,$userMGR->currentPosition()->getDefaultArea());
            $userMGR->sendSmsToGroup('CeremonailMNGDashboard','CEREMONIAL',$des,$userMGR->currentPosition()->getDefaultArea());
            return $this->redirectToRoute('ceremonialREQVisaView',['id'=>$visa->getId(),'msg'=>1]);
        }
        return $this->render('cmr/visa/REQVisaNew.html.twig',[
            'mlist'=>$mlist,
            'form'=>$form->createView(),
            'alerts'=>$alerts,
            'userLIsts'=>$entityMGR->findBy('App:CMListUser',['cmlist'=>$mlist])
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

        return $this->render('cmr/visa/REQVisaList.html.twig',
            [
                'visas'=>$visas
            ]);
    }

    /**
     * @Route("/ceremonial/req/Visa/view/{id}/{msg}", name="ceremonialREQVisaView")
     */
    public function ceremonialREQVisaView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,Service\ACC $ACC)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('404');

        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $visa = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');
        elseif ($visa->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');

        $mlist = $entityMGR->find('App:CMList',$visa->getCMlist()->getId());
        $passengers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$mlist]);
        $logMGR->addEvent('CERVISA'.$visa->getId(),'مشاهده','اطلاعات درخواست ویزا','CEREMONIAL',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'درخواست ویزا با موفقیت ثبت شد.']);

        return $this->render('cmr/visa/REQVisaView.html.twig', [
            'passengers' => $passengers,
            'visa'=>$visa,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERVISA'.$visa->getId()),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/ceremonial/req/air/ticket/list", name="ceremonialREQAIRpaneList")
     */
    public function ceremonialREQAIRpaneList(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('404');

        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $tickets = $entityMGR->findBy('App:CMAirTicket',['submitter'=>$userMGR->currentPosition()]);

        return $this->render('cmr/ticket/REQAirTicketsList.html.twig',
            [
                'tickets'=>$tickets
            ]);
    }

    /**
     * @Route("/ceremonial/air/ticket/{ids}/", name="ceremonialAirticketRequest", options={"expose" = true} ,requirements={"ids"=".+"})
     */
    public function ceremonialAirticketRequest($ids,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(!$userMGR->isLogedIn())      return $this->redirectToRoute('userLogin');

        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $idsArray = explode(',',$ids);
        $mlist = new Entity\CMList();
        $mlist->setSubmitter($userMGR->currentPosition());
        $mlist->setDes('Visa Request');
        $mlist->setListLabel('VisaRequest');
        $entityMGR->insertEntity($mlist);

        foreach ($idsArray as $id){
            $passenger = $entityMGR->find('App:CMPassenger',$id);
            if(! is_null($passenger)){
                if($passenger->getSubmitter() == $userMGR->currentPosition()){
                    $listUser = new Entity\CMListUser();
                    $listUser->setCmlist($mlist);
                    $listUser->setCmpassenger($passenger);
                    $entityMGR->insertEntity($listUser);
                }
            }
        }
        if(count($idsArray) != 0)
            return new Response($mlist->getId());
        return new Response('error');

    }


    /**
     * @Route("/ceremonial/req/air/ticket/new/{id}", name="ceremonialREQAIRpaneNew", options={"expose" = true})
     */
    public function ceremonialREQAIRpaneNew($id,Request $request,Service\Jdate $jdate,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('404');

        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $mlist = $entityMGR->find('App:CMList',$id);
        $passengers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$mlist]);
        if(is_null($mlist))
            return $this->redirectToRoute('404');

        elseif ($mlist->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
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
                $ticket->setCmlist($mlist);
                $ticket->setDateSubmit(time());
                $ticket->setArea($userMGR->currentPosition()->getDefaultArea());
                $ticket->setSubmitter($userMGR->currentPosition());
                $ticket->setTicketState($entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>0]));
                $entityMGR->insertEntity($ticket);
                $logMGR->addEvent('CERTICKET'.$ticket->getId(),'ایجاد','درخواست بلیط','CEREMONIAL',$request->getClientIp());
                $des = sprintf('درخواست بلیط هواپیما توسط %s ثبت شد.',$ticket->getSubmitter()->getPublicLabel());
                $url = $this->generateUrl('ceremonialDOINGTicketView',['id'=>$ticket->getId()]);
                $userMGR->addNotificationForGroup('CeremonailMNGDashboard','CEREMONIAL',$des,$url,$userMGR->currentPosition()->getDefaultArea());
                $userMGR->sendSmsToGroup('CeremonailMNGDashboard','CEREMONIAL',$des,$userMGR->currentPosition()->getDefaultArea());
                $userMGR->sendSmsToUser($userMGR->currentPosition(),'درخواست بلیط هواپیما برای شما ثبت شد.');
                return $this->redirectToRoute('ceremonialREQTicketView',['id'=>$ticket->getId(),'msg'=>1]);
            }
        }
        return $this->render('cmr/ticket/reqAIRpane.html.twig',[
            'userLIsts'=>$passengers,
            'form'=>$form->createView(),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/ceremonial/req/ticket/view/{id}/{msg}", name="ceremonialREQTicketView")
     */
    public function ceremonialREQTicketView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('404');

        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $ticket = $entityMGR->find('App:CMAirTicket',$id);
        if(is_null($ticket))
            return $this->redirectToRoute('404');
        elseif ($ticket->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');

        $passengers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$ticket->getCmlist()]);
        $logMGR->addEvent('CERTICKET'.$ticket->getId(),'مشاهده','اطلاعات درخواست بلیط','CEREMONIAL',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'درخواست بلیط با موفقیت ثبت شد.']);

        return $this->render('cmr/ticket/REQTicketView.html.twig', [
            'passengers' => $passengers,
            'ticket'=>$ticket,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERTICKET'.$ticket->getId()),
            'alerts'=>$alerts
        ]);
    }

}
