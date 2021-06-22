<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use http\Client;
use Psr\Log\NullLogger;
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

class CMAController extends AbstractController
{
    /**
     * @Route("/ceremonial/doing/dashboard", name="ceremonialDOINGDashboard")
     */
    public function ceremonialDOINGDashboard(Request $request,Service\Jdate $jdate,EntityManagerInterface $entityManager,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('FRE56','مشاهده','داشبورد سامانه درخواست تشریفات','CEREMONIAL',$request->getClientIp());

        $dayNow = $jdate->jdate('Y/m/d',time());
        $hotels = $entityMGR->findBy('App:HotelingHotel',['area'=>$userMGR->currentPosition()->getDefaultArea()]);
        $roomCount = 0;
        $hn =[];
        $hc =[];
        $hp =[];
        $dipAll = 0;
        $passengerTodayAll = 0;
        foreach ($hotels as $hotel){
            $rooms = $entityMGR->findBy('App:HotelingRoom',['hotel'=>$hotel]);
            $roomCount = $roomCount + count($rooms);
            $dip = 0;
            $passengerCountToday = 0;
            foreach ($rooms as $item){
                $dip += $item->getDep();
                $passengerCountToday += count($entityMGR->findBy('App:HotelingPassenger',['day'=>$dayNow,'hotel'=>$hotel,'room'=>$item]));
            }
            array_push($hn,$hotel->getHotelName());
            array_push($hp,$passengerCountToday);
            array_push($hc,$dip);
            $dipAll += $dip;
            $passengerTodayAll += $passengerCountToday;
        }

        return $this->render('cma/DOINGDashboard.html.twig', [
            'hotels'=>$hotels,
            'roomsCount'=>$roomCount,
            'hn'=>$hn,
            'hc'=>$hc,
            'hp'=>$hp,
            'dipAll'=>$dipAll,
            'passengerTodayAll'=>$passengerTodayAll,
            'reqs' => $entityManager->createQueryBuilder('a')
                ->select('a')
                ->from('App:HotelingPassenger','a')
                ->andWhere('a.day = :tmr')
                ->setParameter('tmr',$jdate->jdate('Y/m/d',time()))
                ->orderBy('a.id', 'DESC')
                ->getQuery()
                ->getResult()
        ]);
    }
    /**
     * @Route("/ceremonial/doing/pasenger/view/{id}", name="ceremonialDOINGpasengerView", options={"expose" = true})
     */
    public function ceremonialDOINGpasengerView($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($passenger))
            return $this->redirectToRoute('404');
        elseif ($passenger->getSubmitter()->getDefaultArea()->getId() != $userMGR->currentPosition()->getDefaultArea()->getId())
            return $this->redirectToRoute('403');
        $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'مشاهده','اطلاعات مسافر','CEREMONIAL',$request->getClientIp());

        return $this->render('cma/passenger/viewInfo.html.twig', [
            'passenger' => $passenger,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERPASSENGER'.$passenger->getId()),
            'docs'=>$passenger->getCMPassengerPersonalDocs(),
            'coutLetters' => $entityMGR->findBy('App:HRMLetterOutCountry',['user'=>$entityMGR->findOneBy('App:SysUser',['nationalCode'=>$passenger->getPcodemeli()])])
        ]);
    }

    /**
     * @Route("/ceremonial/mng/ticket/remove/passenger/{rid}/{pid}/{type}", name="ceremonialDOINGremovePassenger")
     */
    public function ceremonialDOINGremovePassenger($rid,$pid,$type = 'ticket',Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $entityMGR->remove('App:CMListUser',$pid);
        if($type == 'ticket')
            return $this->redirectToRoute('ceremonialDOINGTicketView',['id'=>$rid,'msg'=>3]);
        else
            return $this->redirectToRoute('ceremonialDOINGVisaView',['id'=>$rid,'msg'=>3]);
    }


    /**
     * @Route("/ceremonial/mng/visa/list/{type}", name="ceremonialDOINGVisaList")
     */
    public function ceremonialDOINGVisaList($type = 'all',Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        if($type == 'all'){
            $visas = $entityMGR->findBy('App:CMVisaReq',['area'=>$userMGR->currentPosition()->getDefaultArea()]);
            $typeName = 'آرشیو تمام درخواست‌ها';
        }
        elseif ($type == 'wfd'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>0]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state,'area'=>$userMGR->currentPosition()->getDefaultArea()]);
            $typeName = 'پاسپورت‌های در انتظار تایید';
        }
        elseif ($type == 'accepted'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>1]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state,'area'=>$userMGR->currentPosition()->getDefaultArea()]);
            $typeName = 'پاسپورت‌های تایید شده';
        }
        else
            return $this->redirectToRoute('404');

        return $this->render('cma/visa/DOINGVisaList.html.twig',
            [
                'visas'=>$visas,
                'typeName'=>$typeName
            ]);
    }

    /**
     * @Route("/ceremonial/doing/visa/view/{id}/{msg}", name="ceremonialDOINGVisaView")
     */
    public function ceremonialDOINGVisaView($id,$msg=0,Request $request,Service\Eitaa $eitaa, Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $visa = $entityMGR->find('App:CMVisaReq',$id);
        $visa1 = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');
        $alerts = [];
        if($msg == 3){
            array_push($alerts,['type'=>'success','message'=>'مسافر حذف شد.']);
        }
        $mlist = $entityMGR->find('App:CMList',$visa->getCMlist()->getId());
        $passengers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$mlist]);

        $logMGR->addEvent('CERVISA'.$visa->getId(),'مشاهده','اطلاعات درخواست ویزا','CEREMONIAL',$request->getClientIp());


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
            //send to eitaa
            $string = 'درخواست ویزا ' . $visa->getSubmitter()->getPublicLabel() . ' توسط ' . $userMGR->currentPosition()->getPublicLabel() .' برای افراد ذیل تایید شد. ';
            foreach ($passengers as $key=>$item){
                $string = $string .  ' - ' .$item->getCmpassenger()->getPname() . ' ' . $item->getCmpassenger()->getPfamily() . ' با کد ملی ' . $item->getCmpassenger()->getPcodemeli() . ' ' ;
            }
            $eitaa->sendToGroup('ceremonial', $string);
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
            //send to eitaa
            $string = 'درخواست ویزا ' . $visa->getSubmitter()->getPublicLabel() . ' توسط ' . $userMGR->currentPosition()->getPublicLabel() .' برای افراد ذیل رد شد. ';
            foreach ($passengers as $key=>$item){
                $string = $string .  ' - ' .$item->getCmpassenger()->getPname() . ' ' . $item->getCmpassenger()->getPfamily() . ' با کد ملی ' . $item->getCmpassenger()->getPcodemeli() . ' ' ;
            }
            $eitaa->sendToGroup('ceremonial', $string);

        }
        return $this->render('cma/visa/DOINGVisaView.html.twig', [
            'passengers' => $passengers,
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
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $tickets = $entityMGR->findBy('App:CMAirTicket',['Area'=>$userMGR->currentPosition()->getDefaultArea()],['ticketState'=>'ASC']);
        if($type == 'acp'){
            $ticketState = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>0]);
            $tickets=$entityMGR->findBy('App:CMAirTicket',['Area'=>$userMGR->currentPosition()->getDefaultArea(),'ticketState'=>$ticketState],['ticketState'=>'ASC']);
        }
        return $this->render('cma/ticket/DOINGAirTicketList.html.twig',
            [
                'tickets'=>$tickets
            ]);
    }

    /**
     * @Route("/ceremonial/doing/ticket/view/{id}/{msg}", name="ceremonialDOINGTicketView")
     */
    public function ceremonialDOINGTicketView($id,$msg=0,Request $request,Service\Eitaa $eitaa,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $ticket = $entityMGR->find('App:CMAirTicket',$id);
        if(is_null($ticket))
            return $this->redirectToRoute('404');
        if($ticket->getArea()->getId() != $userMGR->currentPosition()->getDefaultArea()->getId())
            return $this->redirectToRoute('404');

        $passengers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$ticket->getCmlist()]);
        $logMGR->addEvent('CERTICKET'.$ticket->getId(),'مشاهده','اطلاعات درخواست بلیط','CEREMONIAL',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'درخواست با موفقیت رد شد.']);
        if($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'درخواست با موفقیت تایید شد.']);
        if($msg == 3)
            array_push($alerts,['type'=>'success','message'=>'مسافر از لیست درخواست حذف شد.']);

        $form = $this->createFormBuilder($ticket)
            ->add('ARdes', TextareaType::class,['label'=>'توضیحات تکمیلی:','required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();
        $form1 = $this->createFormBuilder($ticket)
            ->add('ARdes', TextareaType::class,['label'=>'توضیحات تکمیلی:','required'=>false])
            ->add('submit1', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        $form1->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setAccepter($userMGR->currentPosition());
            $ticket->setARdate(time());
            $acceptState = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>1]);
            $ticket->setTicketState($acceptState);
            $logMGR->addEvent('CERTICKET'.$ticket->getId(),'تایید درخواست','درخواست بلیط','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست بلیط شما توسط %s تایید شد.',$ticket->getAccepter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQTicketView',['id'=>$ticket->getId()]);
            $userMGR->addNotificationForUser($ticket->getSubmitter(),$des,$url);
            $userMGR->sendSmsToUser($userMGR->currentPosition(),$des);
            // send to HRM for get letters
            if(is_null($ticket->getSubmitter()->getConstractor()) && !is_null($ticket->getDestination()->getPermission())  ){
                $des = sprintf('درخواست بلیط هواپیما برای  %s توسط %s تایید شد.',$ticket->getSubmitter()->getPublicLabel(),$ticket->getAccepter()->getPublicLabel());
                $url = $this->generateUrl('HRMAirTicketView',['id'=>$ticket->getId()]);
                $userMGR->sendSmsToGroup('HRMACCESS','HRM',$des);
                $userMGR->addNotificationForGroup('HRMACCESS','HRM',$des,$url);
            }
            $des = sprintf('درخواست بلیط هواپیما برای  %s توسط %s تایید شد.',$ticket->getSubmitter()->getPublicLabel(),$ticket->getAccepter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialOPTTicketView',['id'=>$ticket->getId()]);
            $userMGR->sendSmsToGroup('CeremonailOPTDashboard','CEREMONIAL',$des);
            $userMGR->addNotificationForGroup('CeremonailOPTDashboard','CEREMONIAL',$des,$url);
            //send to eitaa
            $string = 'درخواست بلیط هواپیما ' . $ticket->getSubmitter()->getPublicLabel(). ' توسط ' .$userMGR->currentPosition()->getPublicLabel() . ' از مبدا ' . $ticket->getSource()->getCname() . ' به مقصد ' . $ticket->getDestination()->getCname() .' با تاریخ پرواز ' . $ticket->getDateSuggest() .  ' برای افزاد ذیل تایید شد .';
            foreach ($passengers as $key=>$item){
                $string = $string .  ' - ' .$item->getCmpassenger()->getPname() . ' ' . $item->getCmpassenger()->getPfamily() . ' با کد ملی ' . $item->getCmpassenger()->getPcodemeli() . ' ' ;
            }
            $eitaa->sendToGroup('ceremonial', $string);
            return $this->redirectToRoute('ceremonialDOINGTicketView',['id'=>$ticket->getId(),'msg'=>2]);

        }

        if ($form1->isSubmitted() && $form1->isValid()) {
            $ticket->setRejecter($userMGR->currentPosition());
            $ticket->setARdate(time());
            $rejectstate = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>-1]);
            $ticket->setTicketState($rejectstate);
            $entityMGR->update($ticket);
            $logMGR->addEvent('CERTICKET'.$ticket->getId(),'رد درخواست','درخواست بلیط','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست بلیط شما توسط %s رد شد.',$ticket->getRejecter()->getPublicLabel());
            $userMGR->sendSmsToUser($userMGR->currentPosition(),$des);
            $url = $this->generateUrl('ceremonialREQTicketView',['id'=>$ticket->getId()]);
            $userMGR->addNotificationForUser($ticket->getSubmitter(),$des,$url);
            //send to eitaa
            $string = 'درخواست بلیط هواپیما ' . $ticket->getSubmitter()->getPublicLabel(). ' توسط ' .$userMGR->currentPosition()->getPublicLabel() . ' از مبدا ' . $ticket->getSource()->getCname() . ' به مقصد ' . $ticket->getDestination()->getCname() .' با تاریخ پرواز ' . $ticket->getDateSuggest() .  ' برای افزاد ذیل رد شد .';
            foreach ($passengers as $key=>$item){
                $string = $string .  ' - ' .$item->getCmpassenger()->getPname() . ' ' . $item->getCmpassenger()->getPfamily() . ' با کد ملی ' . $item->getCmpassenger()->getPcodemeli() . ' ' ;
            }
            $eitaa->sendToGroup('ceremonial', $string);
            return $this->redirectToRoute('ceremonialDOINGTicketView',['id'=>$ticket->getId(),'msg'=>1]);
        }

        return $this->render('cma/ticket/DOINGTicketView.html.twig', [
            'passengers' => $passengers,
            'ticket'=>$ticket,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERTICKET'.$ticket->getId()),
            'alerts'=>$alerts,
            'form'=>$form->createView(),
            'form1'=>$form1->createView()
        ]);
    }

    /**
     * @Route("/ceremonial/doing/acc/balance/{msg}", name="ceremonialDOINGACCBalance")
     */
    public function ceremonialDOINGACCBalance($msg=0,Request $request,Service\ACC $ACC,Service\LogMGR $logMGR, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $moneyLabels=[];
        $moneyTypes = $entityMGR->findAll('App:ACCMoney');
        $moneyTotal = [];
        foreach ($moneyTypes as $moneyType)
        {
            $tickets = $entityMGR->findBy('App:CMAirTicket',['Area'=>$userMGR->currentPosition()->getDefaultArea(),'moneyType'=>$moneyType]);
            array_push($moneyLabels, $moneyType->getMoneyName());
            $total = 0;
            foreach ($tickets as $ticket){
                $total = $total + $ticket->getMoneyValue();
            }
            array_push($moneyTotal,$total);
        }

        return $this->render('cma/ticket/DOINGACCTicket.html.twig', [
            'moneys' => $moneyTypes,
            'moneyLabels'=>$moneyLabels,
            'moneyTotals'=>$moneyTotal,
            'tickets'=>$entityMGR->findBy('App:CMAirTicket',['Area'=>$userMGR->currentPosition()->getDefaultArea()])
        ]);
    }

}
