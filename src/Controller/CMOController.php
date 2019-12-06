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
}
