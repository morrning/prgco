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

class VisaController extends AbstractController
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
        if($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'اعلام ارسال پاسپورت به کنسولگری با موفقیت ثبت شد.']);
        elseif($msg == 3)
            array_push($alerts,['type'=>'success','message'=>'اعلام دریافت پاسپورت از کنسول گری با موفقیت ثبت شد.']);
        elseif($msg == 1)
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
        $visaState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>3]);
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

    /**
     * @Route("/ceremonial/opt/visa/consulate/{id}/{type}", name="ceremonialOPTVisaConsulate")
     */
    public function ceremonialOPTVisaConsulate($id,$type='in',Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');

        $visa = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');
        $passenger = $visa->getPassenger();
        if($type == 'in'){
            $visaState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>5]);
            $visa->setVisaState($visaState);
            $visa->setDateInputConsulate(time());
            $visa->setConsulateImporter($userMGR->currentPosition());
            $entityMGR->update($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'تحویل گذرنامه به کنسولگری','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $des = sprintf('گذرنامه توسط %s به کنسولگری تحویل داده شد.',$visa->getSubmitter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);
            return $this->redirectToRoute('ceremonialOPTVisaView',['id'=>$visa->getId(),'msg'=>2]);
        }
        if($type == 'out'){
            $visaState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>6]);
            $visa->setVisaState($visaState);
            $visa->setDateOutputConsulate(time());
            $visa->setConsulateExporter($userMGR->currentPosition());
            $entityMGR->update($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'تحویل از کنسولگری','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $des = sprintf('گذرنامه توسط %s از کنسولگری تحویل گرفته شد.',$visa->getSubmitter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);
            return $this->redirectToRoute('ceremonialOPTVisaView',['id'=>$visa->getId(),'msg'=>3]);
        }
    }
}
