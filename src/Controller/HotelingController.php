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

class HotelingController extends AbstractController
{
    /**
     * @Route("/hoteling/opt", name="hotelingOPTDashboard")
     */
    public function hotelingOPTDashboard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        return $this->render('hoteling/OPTDashboard.html.twig', [

        ]);
    }

    /**
     * @Route("/hoteling/opt/hotel/list/{msg}", name="hotelingHotelList")
     */
    public function hotelingHotelList($msg = 0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'اتاق با موفقیت افزوده شد.']);

        return $this->render('hoteling/OPThotels.html.twig',
            [
                'hotels'=>$entityMGR->findBy('App:hotelingHotel',['area'=>$userMGR->currentPosition()->getDefaultArea()]),
                'alerts'=>$alerts
            ]);
    }

    /**
     * @Route("/hoteling/opt/hotel/add/room/{id}", name="hotelingHotelAddRoom")
     */
    public function hotelingHotelAddRoom($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $hotel = $entityMGR->find('App:hotelingHotel',$id);
        if(is_null($hotel))
            return $this->redirectToRoute('404');

        $room = new Entity\HotelingRoom();
        $room->setHotel($hotel);
        $room->setIsFull(false);

        $form = $this->createFormBuilder($room)
            ->add('num', TextType::class,['label'=>'شماره اتاق'])
            ->add('dep', TextType::class,[
                'label'=>'ظرفیت اقامتگاه',
                'attr'     => array(
                    'class'  => 'codeMeli',
                ),
            ])
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            if (is_null($entityMGR->findOneBy('App:hotelingRoom', ['hotel' => $hotel,'num'=>$form->get('num')->getData()]))) {
                $entityMGR->insertEntity($room);
                $logMGR->addEvent('HOTLING' . $hotel->getId() . 'ROOM'.$room->getId(),'افزودن','اطلاعات اتاق','HOTELING',$request->getClientIp());
                return $this->redirectToRoute('hotelingHotelList', ['msg' => 1]);
            }
            $alert = [['type' => 'danger', 'message' => 'این شماره اتاق قبلا ثبت شده است.']];
        }
        return $this->render('hoteling/OPTaddRoom.html.twig', [
            'alerts'=>$alert,
            'form' => $form->createView()
        ]);
    }
}
