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

class CMArbaeinController extends AbstractController
{
    /**
     * @Route("/c/m/arbaein/dashboard", name="cmarbainDashboard")
     */
    public function cmarbainDashboard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CMOPTARBAEIN','CMARBAEIN',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        return $this->render('cm_arbaein/dashboard.html.twig', [

        ]);
    }

    /**
     * @Route("/c/m/arbaein/new/card", name="cmarbainNewCard")
     */
    public function cmarbainNewCard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,Service\Jdate $jdate)
    {
        if(! $userMGR->hasPermission('CMOPTARBAEIN','CMARBAEIN',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $zaer = new Entity\CMArbaein();
        $form = $this->createFormBuilder($zaer)
            ->add('codemeli', TextType::class,['label'=>'کد ملی:','required'=>true,'attr'=>['class'=>'autoClear activeInput']])
            ->add('FGUID', TextType::class,['label'=>'کد فرم:','required'=>true,'attr'=>['class'=>'autoClear']])
            ->add('CGUID', TextType::class,['label'=>'کد کارت:','required'=>true,'attr'=>['class'=>'autoClear']])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(!is_null($entityMGR->findOneBy('App:CMArbaein',['FGUID'=>$form->get('FGUID')->getData()])))
                array_push($alerts,['type'=>'danger','message'=>'این فرم قبلا ثبت شده است.']);
            elseif(!is_null($entityMGR->findOneBy('App:CMArbaein',['CGUID'=>$form->get('CGUID')->getData()])))
                array_push($alerts,['type'=>'danger','message'=>'این کارت قبلا ثبت شده است.']);
            else{
                $zaer->setArea($userMGR->currentPosition()->getDefaultArea());
                $zaer->setInputDate(time());
                $zaer->setInputer($userMGR->currentPosition());
                $zaer->setYear($jdate->jdate('Y',time()));
                $entityMGR->insertEntity($zaer);
                array_push($alerts,['type'=>'success','message'=>'کارت با موفقیت صادر شد.شماره جانمایی گذرنامه ' . $zaer->getId() . ' می باشد.']);
            }
        }
        return $this->render('cm_arbaein/newcard.html.twig', [
            'form'=>$form->createView(),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/c/m/arbaein/searchinfo/card", name="cmarbainSearchInfoCard")
     */
    public function cmarbainSearchInfoCard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,Service\Jdate $jdate)
    {
        if(! $userMGR->hasPermission('CMOPTARBAEIN','CMARBAEIN',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $zaer = new Entity\CMArbaein();
        $form = $this->createFormBuilder($zaer)
            ->add('codemeli', TextType::class,['label'=>'کد ملی:','required'=>true,'attr'=>['class'=>'autoClear activeInput']])
            ->add('submit', SubmitType::class,['label'=>'جست و جو'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(is_null($entityMGR->findOneBy('App:CMArbaein',['codemeli'=>$form->get('codemeli')->getData()])))
                array_push($alerts,['type'=>'danger','message'=>'زائری با مشخصات وارد شده ثبت نشده است.']);
            else
                return $this->redirectToRoute('cmarbainSCompleteCard',['id'=>$form->get('codemeli')->getData()]);
        }
        return $this->render('cm_arbaein/searchcard.html.twig', [
            'form'=>$form->createView(),
            'alerts'=>$alerts
        ]);

    }

    /**
     * @Route("/c/m/arbaein/complete/card/{id}", name="cmarbainSCompleteCard")
     */
    public function cmarbainSCompleteCard($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,Service\Jdate $jdate)
    {
        if(! $userMGR->hasPermission('CMOPTARBAEIN','CMARBAEIN',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $zaer = $entityMGR->findOneBy('App:CMArbaein',['FGUID'=>$id]);

        if(is_null($zaer))
            return $this->redirectToRoute('404');
        $exist = $entityMGR->findOneBy('App:CMArbaein',['codemali'=>$zaer->getCodemeli()]);
        if(is_null($exist)){
            $form = $this->createFormBuilder($zaer)
                ->add('fullname', TextType::class,['label'=>'نام و نام خانوادگی:','required'=>true,'attr'=>['class'=>'activeInput']])
                ->add('birthday', TextType::class,['label'=>'سال تولد:','required'=>true])
                ->add('tel', TextType::class,['label'=>'شماره تماس:','required'=>true,'attr'=>['class'=>'tel']])
                ->add('isMan', ChoiceType::class,[
                    'label'=>'جنسیت:',
                    'choices'=>['مرد'=>true,'زن'=>false],
                ])
                ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
                ->getForm();
        }
        else{
            $form = $this->createFormBuilder($zaer)
                ->add('fullname', TextType::class,['data'=>$exist->getFullname(),'label'=>'نام و نام خانوادگی:','required'=>true,'attr'=>['class'=>'activeInput']])
                ->add('birthday', TextType::class,['data'=>$exist->getBirthday(),'label'=>'سال تولد:','required'=>true])
                ->add('tel', TextType::class,['data'=>$exist->getBirthday(),'label'=>'شماره تماس:','required'=>true,'attr'=>['class'=>'tel']])
                ->add('isMan', ChoiceType::class,[
                    'label'=>'جنسیت:',
                    'choices'=>['مرد'=>true,'زن'=>false],
                ])
                ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
                ->getForm();
        }

        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $zaer->setCompleter($userMGR->currentPosition());
            $entityMGR->update($zaer);
            return $this->redirectToRoute('cmarbainSearchInfoCard');
        }

        return $this->render('cm_arbaein/completeInfo.html.twig', [
            'form'=>$form->createView(),
            'alerts'=>$alerts,
            'zaer'=>$zaer
        ]);
    }
}
