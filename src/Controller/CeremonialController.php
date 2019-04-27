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
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Form\Type as Type;


use App\Service;
use App\Entity;

class CeremonialController extends AbstractController
{
    /**
     * @Route("/ceremonial/req/dashboard", name="ceremonialREQDashboard")
     */
    public function ceremonialREQDashboard(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        return $this->render('ceremonial/REQDashboard.html.twig', [
            'controller_name' => 'CeremonialController',
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasengers", name="ceremonialREQpasengers")
     */
    public function ceremonialREQpasengers(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        return $this->render('ceremonial/REQPassengers.html.twig', [
            'passengers' => $userMGR->currentPosition()->getcMPassengers()
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasenger/view/{id}", name="ceremonialREQpasengerView")
     */
    public function ceremonialREQpasengerView($id,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($passenger))
            return $this->redirectToRoute('404');
        elseif ($passenger->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');

        return $this->render('ceremonial/viewPassengerInfo.html.twig', [
            'passenger' => $passenger
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasenger/new", name="ceremonialREQpasengerNew")
     */
    public function ceremonialREQpasengerNew(Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $passenger = new Entity\CMPassenger();
        $form = $this->createFormBuilder($passenger)
            ->add('label', TextType::class,['label'=>'عنوان پست سازمانی'])
            ->add('defaultArea', EntityType::class, [
                'class'=>Entity\SysArea::class,
                'choice_label'=>'areaName',
                'choice_value' => 'id',
                'label'=>'ناحیه کاری',
                'data'=>$entityMGR->find('App:SysArea',$position->getDefaultArea()->getId()),
            ])
            ->add('userID', Type\AutoentityType::class,['class'=>'App:SysUser','choice_label'=>'fullName','label'=>'نام کاربر','attr'=>['pattern'=>'users']])
            ->add('upperID', Type\AutocompleteType::class,['label'=>'پست سازمانی بالادستی','attr'=>['pattern'=>'positions']])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }
        return $this->render('ceremonial/newPasenger.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
