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


class HSSEController extends AbstractController
{

    //------------------------------------------ HSSE TOTAL -----------------------------------------
    /**
     * @Route("/hsse/dashboard", name="HSSEDashboard")
     */
    public function HSSEDashboard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if($userMGR->hasPermission('HSSETOTAL','HSSE') || $userMGR->hasPermission('HSSEAREA','HSSE'))
        {
            return $this->render('hsse/HSEDashboard.html.twig', [
                'controller_name' => 'HSSEController',
            ]);
        }
        return $this->redirectToRoute('403');
    }

    /**
     * @Route("/hsse/ceremonial/visa/list/{type}", name="HSSECeremonialVisaList")
     */
    public function HSSECeremonialVisaList($type = 'all',Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSETOTAL','HSSE'))
            return $this->redirectToRoute('403');
        if($type == 'all'){
            $visas = $entityMGR->findAll('App:CMVisaReq');
            $typeName = 'آرشیو تمام درخواست‌ها';
        }
        elseif ($type == 'wfa'){
            $state = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>2]);
            $visas = $entityMGR->findBy('App:CMVisaReq',['visaState'=>$state]);
            $typeName = 'پاسپورت‌های منتظر تایید';
        }
        else
            return $this->redirectToRoute('404');

        return $this->render('hsse/HSEVisaList.html.twig',
            [
                'visas'=>$visas,
                'typeName'=>$typeName
            ]);
    }

    /**
     * @Route("/hsse/ceremonial/visa/view/{id}", name="HSSECeremonialView")
     */
    public function HSSECeremonialView($id,$msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSETOTAL','HSSE'))
            return $this->redirectToRoute('403');
        $visa = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');
        $passenger = $entityMGR->find('App:CMPassenger',$visa->getPassenger()->getId());
        $logMGR->addEvent('CERVISA'.$visa->getId(),'مشاهده','اطلاعات درخواست ویزا','CEREMONIAL',$request->getClientIp());
        $alerts = [];

        $form = $this->createFormBuilder($visa)
            ->add('hseedu', ChoiceType::class, [
                'label'=>'آیا نامبرده نیازمند آموزش‌های ایمنی و اقدامات تامینی است؟',
                'choices'  => [
                    'عدم نیاز به آموزش' => 'عدم نیاز به آموزش',
                    'نیازمند آموزش‌ ایمنی و اقدامات تامینی' => 'نیازمند آموزش‌ ایمنی و اقدامات تامینی',
                ],
            ])
            ->add('hseDes', TextareaType::class,['label'=>'توضیحات تکمیلی:','required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();
        $form1 = $this->createFormBuilder($visa)
            ->add('hseedu', TextareaType::class,['label'=>'توضیحات تکمیلی:','required'=>false])
            ->add('submit1', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        $form1->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $visa->setHseSubmitDate(time());
            $visa->setHseAR($userMGR->currentPosition());
            $acceptState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>3]);
            $visa->setVisaState($acceptState);
            $visa->setHseState('مورد تایید');
            $entityMGR->update($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'تایید','ایمنی و اقدامات تامینی','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست ویزای شما از نظر ایمنی و اقدامات تامینی توسط %s تایید شد.',$visa->getHseAR()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);

            $des = sprintf('درخواست ویزا از نظر ایمنی و اقدامات تامینی توسط %s تایید شد.',$visa->getHseAR()->getPublicLabel());
            $url = $this->generateUrl('ceremonialDOINGVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForGroup('CeremonailMNGDashboard','CEREMONIAL',$des,$url);
            array_push($alerts,['type'=>'success','message'=>'درخواست ویزا با موفقیت تایید شد.']);        }

        if ($form1->isSubmitted() && $form1->isValid()) {
            $visa->setHseSubmitDate(time());
            $visa->setHseAR($userMGR->currentPosition());
            $acceptState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>-1]);
            $visa->setVisaState($acceptState);
            $visa->setHseState('عدم تایید');
            $entityMGR->update($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'عدم تایید','ایمنی و اقدامات تامینی','CEREMONIAL',$request->getClientIp());
            $des = sprintf('درخواست ویزای شما از نظر ایمنی و اقدامات تامینی توسط %s رد شد.',$visa->getHseAR()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);

            array_push($alerts,['type'=>'success','message'=>'درخواست ویزا با موفقیت رد شد.']);
        
        }

        return $this->render('hsse/HSEVisaView.html.twig', [
            'passenger' => $passenger,
            'visa'=>$visa,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERVISA'.$visa->getId()),
            'alerts'=>$alerts,
            'form'=>$form->createView(),
            'form1'=>$form1->createView()
        ]);
    }
}
