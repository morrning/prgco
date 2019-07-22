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
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'درخواست بلیط با موفقیت ثبت شد.']);

        return $this->render('ceremonial/viewPassengerInfo.html.twig', [
            'passenger' => $passenger,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERPASSENGER'.$passenger->getId()),
            'alerts'=>$alerts
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
            ->add('des', TextareaType::class,['label'=>'توضیحات تکمیلی:'])
            ->add('submit', SubmitType::class,['label'=>'ثبت درخواست'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setPassengerID($passenger);
            $ticket->setDateSubmit(time());
            $ticket->setArea($userMGR->currentPosition()->getDefaultArea());
            $ticket->setSubmitter($userMGR->currentPosition());
            $entityMGR->insertEntity($ticket);
            $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','درخواست بلیط هواپیما','CEREMONIAL',$request->getClientIp());
            return $this->redirectToRoute('ceremonialREQpasengerView',['id'=>$passenger->getId(),'msg'=>1]);
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
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
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
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
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
}
