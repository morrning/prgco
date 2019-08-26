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
    public function HSSEDashboard()
    {
        return $this->render('hsse/HSEDashboard.html.twig', [
            'controller_name' => 'HSSEController',
        ]);
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
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'درخواست ویزا با موفقیت ثبت شد.']);

        return $this->render('hsse/HSEVisaView.html.twig', [
            'passenger' => $passenger,
            'visa'=>$visa,
            'events'=>$logMGR->getEvents('CEREMONIAL','CERVISA'.$visa->getId()),
            'alerts'=>$alerts
        ]);
    }
}
