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

        return $this->render('ceremonial/viewPassengerInfo.html.twig', [
            'passenger' => $passenger,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERPASSENGER'.$passenger->getId())
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
            ->add('pcodemeli', TextType::class,['label'=>'کد ملی'])
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
        $jdate = new Service\Jdate();
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            if (is_null($entityMGR->findOneBy('App:CMPassenger', ['pcodemeli' => $passenger->getPcodemeli()]))) {
                $passenger->setSubmitter($userMGR->currentPosition());
                $passenger->setPbirthday($jdate->jallaliToUnixTime($passenger->getPbirthday()));
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
            ->add('lfamily', TextType::class,['label'=>'Family:'])
            ->add('lfather', TextType::class,['label'=>'Father Name:', 'required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        $jdate = new Service\Jdate();
        if ($form->isSubmitted() && $form->isValid()) {
            $passenger->setSubmitter($userMGR->currentPosition());
            $passenger->setPbirthday($jdate->jallaliToUnixTime($passenger->getPbirthday()));
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
    public function ceremonialREQAIRpaneNew($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
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
            ->add('dateSuggest',Type\JdateType::class,['label'=>'تاریخ مسافرت:'])
            ->add('des', TextareaType::class,['label'=>'علت سفر:','required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت درخواست'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
        return $this->render('ceremonial/reqAIRpane.html.twig',[
            'passenger'=>$passenger,
            'form'=>$form->createView()
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
     * @Route("/ceremonial/doing/air/ticket/list", name="ceremonialDOINGAIRpaneList")
     */
    public function ceremonialDOINGAIRpaneList(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');
        $tickets = $entityMGR->findBy('App:CMAirTicket',[],['ticketState'=>'ASC']);

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

        $form = $this->createFormBuilder($ticket)
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
            ->add('moneyValue', Type\NumbermaskType::class,['label'=>'مقدار هزینه'])
            ->add('flyDate',Type\JdateType::class,['label'=>'تاریخ پرواز'])
            ->add('flyTime', TimeType::class, [
                'label'=>'ساعت پرواز',
                'input'  => 'string',
                'widget' => 'single_text',
                'with_seconds'=>false
            ])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setBuyer($userMGR->currentPosition());
            $ticket->setBuyDate(time());
            $acceptState = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>3]);
            $ticket->setTicketState($acceptState);

            //accounting process
            if($ticket->getAcceptIF()->getIfCode()==1)
                $account = $ACC->getAccountByAccountNo(1);
            else{
                if($ACC->hasAccount($ticket->getSubmitter()))
                    $account = $ACC->getAccountByUser($ticket->getSubmitter()->getUserID());
                else
                    $account = $ACC->addAccount($ticket->getSubmitter()->getUserID()->getFullname(),$ticket->getSubmitter()->getUserID());
            }

            $ic = $ACC->getICCenter(1002);
            $document = $ACC->addDocument('ایاب و ذهاب',$userMGR->currentPosition(),$ic,$account);
            $ACC->addDocumentItem($document,$ticket->getMoneyType(),$ticket->getMoneyValue());

            $logMGR->addEvent('CERTICKET'.$ticket->getId(),'خرید بلیط','درخواست بلیط','CEREMONIAL',$request->getClientIp());
            $des = sprintf(' بلیط شما توسط %s خریداری شد.',$ticket->getBuyer()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQTicketView',['id'=>$ticket->getId()]);
            $userMGR->addNotificationForUser($ticket->getSubmitter(),$des,$url);
        }

        return $this->render('ceremonial/OPTTicketView.html.twig', [
            'travels'=>$entityMGR->findBy('App:CMAirTicket',['passengerID'=>$passenger]),
            'passenger' => $passenger,
            'ticket'=>$ticket,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERTICKET'.$ticket->getId()),
            'alerts'=>$alerts,
            'form'=>$form->createView(),
        ]);
    }

}
