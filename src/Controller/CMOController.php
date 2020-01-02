<?php

namespace App\Controller;

use http\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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

class CMOController extends AbstractController
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
     * @Route("/ceremonial/opt/dashboard", name="ceremonialOPTDashboard")
     */
    public function ceremonialOPTDashboard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('FRE56','مشاهده','داشبورد سامانه درخواست تشریفات','CEREMONIAL',$request->getClientIp());

        return $this->render('cmo/OPTDashboard.html.twig', [

        ]);
    }

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
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>0]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state]);
            $typeName = 'درخواست های ویزا در انتظار تایید';
        }
        elseif ($type == 'acp'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>1]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state]);
            $typeName = 'درخواست های ویزا تایید شده';
        }
        elseif ($type == 'rej'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>-1]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state]);
            $typeName = 'درخواست های ویزا رد شده';
        }
        else
            return $this->redirectToRoute('404');

        return $this->render('cmo/visa/OPTVisaList.html.twig',
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

        $mlist = $entityMGR->find('App:CMList',$visa->getCMlist()->getId());
        $passengers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$mlist]);

        $logMGR->addEvent('CERVISA'.$visa->getId(),'مشاهده','اطلاعات درخواست ویزا','CEREMONIAL',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'اعلام ارسال پاسپورت به کنسولگری با موفقیت ثبت شد.']);

        $form = $this->createFormBuilder($visa)
            ->add('moneyType', EntityType::class, [
                'class'=>Entity\ACCMoney::class,
                'choice_label'=>'moneyName',
                'choice_value' => 'id',
                'label'=>'واحد پول'
            ])
            ->add('visaType', EntityType::class, [
                'class'=>Entity\CMVisaType::class,
                'choice_label'=>'typeName',
                'choice_value' => 'id',
                'label'=>'نوع ویزا'
            ])
            ->add('DateStart',Type\JdateType::class,['label'=>'شروع اعتبار ویزا'])
            ->add('DateEnd',Type\JdateType::class,['label'=>'پایان اعتبار ویزا'])
            ->add('moneyValue', NumberType::class,['label'=>'مبلغ:','data'=>0,'required'=>true,'attr'=>['class'=>'MoneyInput']])
            ->add('submit', SubmitType::class,['label'=>'ثبت اعلام وصول ویزا'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $visa->setBuyDate(time());
            $visa->setBuyer($userMGR->currentPosition());
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>2]);
            $visa->setVisaState($state);
            $entityMGR->update($visa);

            //set visa Log for passengers
            $listUsers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$visa->getCmlist()]);
            foreach ($listUsers as $listUser){
                $userLog = new Entity\CMVisaLog();
                $userLog->setRequestID($visa);
                $userLog->setCountry($visa->getCountryDes());
                $userLog->setDateStart($visa->getDateStart());
                $userLog->setDateEnd($visa->getDateEnd());
                $userLog->setPassenger($listUser->getCMPassenger());
                $userLog->setVisaType($visa->getVisaType());
                $entityMGR->insertEntity($userLog);
            }
            //system notifications and logs
            $logMGR->addEvent('CERVISA'.$visa->getId(),'اعلام وصول ویزا','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $des = sprintf(' ویزای شما توسط %s اعلام وصول شد.',$userMGR->currentPosition()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);
            array_push($alerts,['type'=>'success','message'=>'اعلام ارسال ویزا با موفقیت ثبت شد.']);
        }
        return $this->render('cmo/visa/OPTVisaView.html.twig', [
            'passengers' => $passengers,
            'visa'=>$visa,
            'form'=>$form->createView(),
            'events'=>$logMGR->getEvents('CEREMONIAL','CERVISA'.$visa->getId()),
            'alerts'=>$alerts
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

        $mlist = $entityMGR->find('App:CMList',$ticket->getCMlist()->getId());
        $passengers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$mlist]);

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
            ->add('fileID', Type\FileboxType::class,['label'=>'فایل اسکن','data_class'=>null])
            ->add('moneyValue', Type\NumbermaskType::class,['data'=>0,'label'=>'مقدار هزینه','attr'=>['class'=>'MoneyInput']])
            ->add('flyDate',Type\JdateType::class,['label'=>'تاریخ پرواز','data'=>$ticket->getDateSuggest()])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $guid = $this->RandomString(32);
            $file = $form->get('fileID')->getData();
            if($file->getClientOriginalExtension() == 'pdf'  || $file->getClientOriginalExtension() == 'jpg' || $file->getClientOriginalExtension() == 'jpeg' || $file->getClientOriginalExtension() == 'png'){
                if($file->getSize() < 4097152){
                    $tempFileName = $guid . '.' . $file->getClientOriginalExtension();
                    $file->move(str_replace('src','public_html',dirname(__DIR__)) . '/files',$tempFileName );
                    $ticket->setFileID($tempFileName);
                    $ticket->setMoneyValue(str_replace(',','',$ticket->getMoneyValue()));
                    $ticket->setBuyer($userMGR->currentPosition());
                    $ticket->setBuyDate(time());
                    $acceptState = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>2]);
                    $ticket->setTicketState($acceptState);
                    $entityMGR->update($ticket);
                    $logMGR->addEvent('CERTICKET'.$ticket->getId(),'خرید بلیط','درخواست بلیط','CEREMONIAL',$request->getClientIp());
                    $des = sprintf(' بلیط شما توسط %s خریداری شد.',$ticket->getBuyer()->getPublicLabel());
                    $url = $this->generateUrl('ceremonialREQTicketView',['id'=>$ticket->getId()]);
                    $userMGR->addNotificationForUser($ticket->getSubmitter(),$des,$url);
                    array_push($alerts,['type'=>'success','message'=>'اطلاعات بلیط با موفقیت ثبت شد.']);
                }
                else{
                    array_push($alerts,['type'=>'danger','message'=>'فایل ارسال شده بسیار حجیم است.حداکثر حجم ارسال فایل 2 مگابایت می باشد.']);
                }
            }
            else{
                array_push($alerts, ['type'=>'danger','message'=>'نوع فایل وارد شده صحیح نیست.لطفا فایل ,png,pdf,jpeg  ارسال فرمایید.']);
            }

        }

        return $this->render('cmo/ticket/OPTTicketView.html.twig', [
            'passengers' => $passengers,
            'ticket'=>$ticket,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERTICKET'.$ticket->getId()),
            'alerts'=>$alerts,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/ceremonial/opt/air/ticket/list/{type}", name="ceremonialOPTAIRpaneList")
     */
    public function ceremonialOPTAIRpaneList($type,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');

        $tickets = $entityMGR->findBy('App:CMAirTicket',[],['dateSubmit'=>'DESC']);

        if($type == 'acp'){
            $state = $entityMGR->findOneBy('App:CMAirTicketState',['StateCode'=>'1']);
            $tickets = $entityMGR->findBy('App:CMAirTicket',['ticketState'=>$state],['dateSubmit'=>'DESC']);
        }
        return $this->render('cmo/ticket/OPTAirTicketList.html.twig',
            [
                'tickets'=>$tickets,
                'type'=>$type
            ]);
    }
}
