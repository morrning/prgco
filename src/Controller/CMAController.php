<?php

namespace App\Controller;

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
    public function ceremonialDOINGDashboard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('FRE56','مشاهده','داشبورد سامانه درخواست تشریفات','CEREMONIAL',$request->getClientIp());

        return $this->render('cma/DOINGDashboard.html.twig', [
            'controller_name' => 'CeremonialController',
        ]);
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
            $visas = $entityMGR->findAll('App:CMVisaReq');
            $typeName = 'آرشیو تمام درخواست‌ها';
        }
        elseif ($type == 'wfd'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>0]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state]);
            $typeName = 'پاسپورت‌های در انتظار تایید';
        }
        elseif ($type == 'accepted'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>1]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state]);
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
    public function ceremonialDOINGVisaView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $visa = $entityMGR->find('App:CMVisaReq',$id);
        $visa1 = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');

        $mlist = $entityMGR->find('App:CMList',$visa->getCMlist()->getId());
        $passengers = $entityMGR->findBy('App:CMListUser',['cmlist'=>$mlist]);

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
            $visa->setADDateSubmit(time());
            $visa->setAccepter($userMGR->currentPosition());
            $acceptState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>1]);
            $visa->setVisaState($acceptState);
            $entityMGR->update($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'تایید','مدیریت تشریفات','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست ویزای شما توسط %s تایید شد.',$userMGR->currentPosition()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);

            $des = 'درخواست ویزا توسط مدیریت تایید شد.';
            $url = $this->generateUrl('ceremonialOPTVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForGroup('CeremonailOPTDashboard','CEREMONIAL',$des,$url);

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

            array_push($alerts,['type'=>'success','message'=>'درخواست ویزا با موفقیت رد شد.']);

        }
        return $this->render('cma/visa/DoingVisaView.html.twig', [
            'passengers' => $passengers,
            'visa'=>$visa,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERVISA'.$visa->getId()),
            'alerts'=>$alerts,
            'form'=>$form->createView(),
            'form1'=>$form1->createView()
        ]);
    }
}
