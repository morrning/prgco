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



    //--------------------------------------------MANAGER PART ----------------------------------------


    /**
     * @Route("/ceremonial/doing/acc/balance/{msg}", name="ceremonialDOINGACCBalance")
     */
    public function ceremonialDOINGACCBalance($msg=0,Request $request,Service\ACC $ACC,Service\LogMGR $logMGR, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');

        $moneyLabels=[];
        $moneyTypes = $entityMGR->findAll('App:ACCMoney');
        $moneyTotal = [];
        $ic = $entityMGR->findOneBy('App:ACCiccenter',['iccode'=>1002]);
        $totalDocs = $entityMGR->findBy('App:ACCdoc',['iccenter'=>$ic]);
        foreach ($moneyTypes as $moneyType)
        {
            array_push($moneyLabels, $moneyType->getMoneyName());
            $docs = $entityMGR->findBy('App:ACCdoc',['iccenter'=>$ic,'Money'=>$moneyType]);
            $total = 0;
            foreach ($docs as $doc){
                $total = $total + $doc->getTotalValue();
            }
            array_push($moneyTotal,$total);
        }

        return $this->render('ceremonial/DOINGACCTicket.html.twig', [
            'moneys' => $moneyTypes,
            'moneyLabels'=>$moneyLabels,
            'moneyTotals'=>$moneyTotal,
            'docs'=>$totalDocs
        ]);
    }



    //--------------------------------------------OPERATOR PART ----------------------------------------


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




}
