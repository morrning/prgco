<?php

namespace App\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Yaml\Yaml;

//json encoder classes
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Service;
use App\Entity as Entity;
use App\Form\Type as Type;

class CountdownController extends AbstractController
{
    /**
     * @Route("/countdown/dashboard/{msg}", name="countdownDashboard")
     */
    public function countdownDashboard($msg = 0,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('CountDown','COUNTDOWN'))
            return $this->redirectToRoute('403');

        $alerts = [];
        if($msg==1)
            $alerts = [['message'=>'روزشمار  با موفقیت اضافه شد.','type'=>'success']];
        elseif($msg==2)
            $alerts = [['message'=>'روزشمار  با موفقیت حذف شد.','type'=>'danger']];
        elseif($msg==3)
            $alerts = [['message'=>'روزشمار  با موفقیت ویرایش شد.','type'=>'success']];

        return $this->render('countdown/dashboard.html.twig', [
            'countdowns' => $entityMGR->findAll('App:Countdown'),
            'alert'=>$alerts
        ]);
    }

    /**
     * @Route("/countdown/delete/{id}", name="countdownDelete")
     */
    public function countdownDelete($id ,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('CountDown','COUNTDOWN'))
            return $this->redirectToRoute('403');
        $entityMGR->remove('App:Countdown',$id);
        return $this->redirectToRoute('countdownDashboard',['msg'=>2]);
    }
    /**
     * @Route("/countdown/new", name="countdownNew")
     */
    public function countdownNew(Request $request, Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('CountDown','COUNTDOWN'))
            return $this->redirectToRoute('403');

        $countDown = new Entity\Countdown();
        $form = $this->createFormBuilder($countDown)
            ->add('title', TextType::class,['label'=>'عنوان'])
            ->add('timeArrive', NumberType::class,['label'=>'تعداد روز تا سررسید'])
            ->add('des', TextType::class,['label'=>'توضیحات'])
            ->add('submit', SubmitType::class,['label'=>'ذخیره'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $countDown->setTimearrive(($countDown->getTimearrive() * 86400) + time());
            $entityMGR->insertEntity($countDown);
            return $this->redirectToRoute('countdownDashboard',['msg'=>1]);
        }


        return $this->render('countdown/newCountdown.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/countdown/edit/{id}", name="countdownEdit")
     */
    public function countdownEdit($id, Request $request, Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('CountDown','COUNTDOWN'))
            return $this->redirectToRoute('403');
        $entity = $entityMGR->find('App:Countdown',$id);
        if(is_null($entity))
            return $this->redirectToRoute('404');

        $timeArrive = ($entity->getTimearrive() - time())/86400;
        $form = $this->createFormBuilder($entity)
            ->add('title', TextType::class,['label'=>'عنوان'])
            ->add('timeArrive', NumberType::class,['label'=>'تعداد روز تا سررسید','data'=>number_format($timeArrive,0,'.',',')])
            ->add('des', TextType::class,['label'=>'توضیحات'])
            ->add('submit', SubmitType::class,['label'=>'ذخیره'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setTimearrive(($entity->getTimearrive() * 86400) + time());
            $entityMGR->update($entity);
            return $this->redirectToRoute('countdownDashboard',['msg'=>3]);
        }

        return $this->render('countdown/newCountdown.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
