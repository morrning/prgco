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
use Doctrine\ORM\EntityManagerInterface;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Form\Type as Type;


use App\Service;
use App\Entity;

class HotelingController extends AbstractController
{
    /**
     * @Route("/hoteling/opt", name="hotelingOPTDashboard")
     */
    public function hotelingOPTDashboard(Request $request,Service\Jdate $jdate,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $dayNow = $jdate->jdate('Y/m/d',time());
        $hotels = $entityMGR->findBy('App:HotelingHotel',['area'=>$userMGR->currentPosition()->getDefaultArea()]);
        $roomCount = 0;
        $hn =[];
        $hc =[];
        $hp =[];
        $dipAll = 0;
        $passengerTodayAll = 0;
        foreach ($hotels as $hotel){
            $rooms = $entityMGR->findBy('App:HotelingRoom',['hotel'=>$hotel]);
            $roomCount = $roomCount + count($rooms);
            $dip = 0;
            $passengerCountToday = 0;
            foreach ($rooms as $item){
                $dip += $item->getDep();
                $passengerCountToday += count($entityMGR->findBy('App:HotelingPassenger',['day'=>$dayNow,'hotel'=>$hotel,'room'=>$item]));
            }
            array_push($hn,$hotel->getHotelName());
            array_push($hp,$passengerCountToday);
            array_push($hc,$dip);
            $dipAll += $dip;
            $passengerTodayAll += $passengerCountToday;
        }
        return $this->render('hoteling/OPTDashboard.html.twig', [
            'hotels'=>$hotels,
            'roomsCount'=>$roomCount,
            'hn'=>$hn,
            'hc'=>$hc,
            'hp'=>$hp,
            'dipAll'=>$dipAll,
            'passengerTodayAll'=>$passengerTodayAll
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
        elseif($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'هتل با موفقیت افزوده شد.']);
        elseif($msg == 3)
            array_push($alerts,['type'=>'success','message'=>'هتل با موفقیت ویرایش شد.']);
        $hotels = $entityMGR->findBy('App:hotelingHotel',['area'=>$userMGR->currentPosition()->getDefaultArea()]);
        foreach ($hotels as $hotel){
            $rooms = $entityMGR->findBy('App:HotelingRoom',['hotel'=>$hotel]);
            $hotel->setDep(0);
            foreach ($rooms as $room){
                $hotel->setDep($hotel->getDep() + $room->getDep());
            }
        }
        return $this->render('hoteling/OPThotels.html.twig',
            [
                'hotels'=>$hotels,
                'alerts'=>$alerts
            ]);
    }
    /**
     * @Route("/hoteling/opt/room/view/{id}", name="hotelingViewRoom")
     */
    public function hotelingViewRoom($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $room = $entityMGR->find('App:HotelingRoom',$id);
        if(is_null($room))
            return $this->redirectToRoute('404');
        $alert = [];

        return $this->render('hoteling/OPTviewRoom.html.twig', [
            'alerts'=>$alert,
            'room'=>$room
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
            ->add('WC', EntityType::class,
                [
                    'label'=>'نوع سرویس بهداشتی',
                    'class'=>Entity\HotelingRoomWCType::class,
                    'choice_value'=>'typeCode',
                    'choice_label'=>'typeName'
                ]
            )
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            if (is_null($entityMGR->findOneBy('App:hotelingRoom', ['hotel' => $hotel,'num'=>$form->get('num')->getData()]))) {
                $room->setCanUse($entityMGR->findOneBy('App:DTyesNo',['typeCode'=>1]));
                $entityMGR->insertEntity($room);
                $logMGR->addEvent('HOTLING' . $hotel->getId() . 'ROOM'.$room->getId(),'افزودن','اطلاعات اتاق','HOTELING',$request->getClientIp());
                return $this->redirectToRoute('hotelingRoomsList', ['id'=>$hotel->getId(),'msg' => 1]);
            }
            $alert = [['type' => 'danger', 'message' => 'این شماره اتاق قبلا ثبت شده است.']];
        }
        return $this->render('hoteling/OPTaddRoom.html.twig', [
            'alerts'=>$alert,
            'form' => $form->createView(),
            'hotel'=>$hotel
        ]);
    }

    /**
     * @Route("/hoteling/opt/hotel/edit/room/{id}", name="hotelingHotelEditRoom")
     */
    public function hotelingHotelEditRoom($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $room = $entityMGR->find('App:HotelingRoom',$id);
        if(is_null($room))
            return $this->redirectToRoute('404');
        if($userMGR->currentPosition()->getDefaultArea() != $room->getHotel()->getArea())
            return $this->redirectToRoute('403');

        $form = $this->createFormBuilder($room)
            ->add('dep', TextType::class,[
                'label'=>'ظرفیت اقامتگاه',
                'attr'     => array(
                    'class'  => 'codeMeli',
                ),
            ])
            ->add('WC', EntityType::class,
                [
                    'label'=>'نوع سرویس بهداشتی',
                    'class'=>Entity\HotelingRoomWCType::class,
                    'choice_value'=>'typeCode',
                    'choice_label'=>'typeName'
                ]
            )
            ->add('canUse', EntityType::class,
                [
                    'label'=>'اتاق فعال است؟',
                    'class'=>Entity\DTyesNo::class,
                    'choice_value'=>'typeCode',
                    'choice_label'=>'typeName'
                ]
            )
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->update($room);
            $logMGR->addEvent('HOTLING' . $room->getHotel()->getId() . 'ROOM'.$room->getId(),',ویرایش','اطلاعات اتاق','HOTELING',$request->getClientIp());
            return $this->redirectToRoute('hotelingRoomsList', ['id'=>$room->getHotel()->getId(),'msg' => 2]);
        }
        return $this->render('hoteling/OPTeditRoom.html.twig', [
            'alerts'=>$alert,
            'form' => $form->createView(),
            'room'=>$room
        ]);
    }

    /**
     * @Route("/hoteling/opt/hotel/add/hotel", name="hotelingHotelAddHotel")
     */
    public function hotelingHotelAddHotel(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $hotel = new Entity\HotelingHotel();
        $hotel->setArea($userMGR->currentPosition()->getDefaultArea());
        $form = $this->createFormBuilder($hotel)
             ->add('hotelName', TextType::class,['label'=>'نام مرکز اسکان:'])
             ->add('adr', TextType::class,['label'=>'آدرس مرکز:'])
             ->add('tel', TextType::class,['label'=>'تلفن تماس:'])
             ->add('des', TextareaType::class,['label'=>'توضیحات بیشتر'])
            ->add('submit', SubmitType::class,['label'=>'افزودن هتل'])
            ->getForm();
        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->insertEntity($hotel);
            $logMGR->addEvent('HOTLING' . $hotel->getId() . 'ROOM'.$hotel->getId(),'افزودن','اطلاعات هتل','HOTELING',$request->getClientIp());
            return $this->redirectToRoute('hotelingHotelList', ['msg' => 2]);
        }
        return $this->render('hoteling/OPThotelNew.html.twig', [
            'alerts'=>$alert,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/hoteling/opt/hotel/edit/hotel/{id}", name="hotelingHotelEditHotel")
     */
    public function hotelingHotelEditHotel($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $hotel = $entityMGR->find('App:hotelingHotel',$id);
        if(is_null($hotel))
            return $this->redirectToRoute('404');

        $form = $this->createFormBuilder($hotel)
            ->add('hotelName', TextType::class,['label'=>'نام هتل:'])
            ->add('adr', TextType::class,['label'=>'آدرس هتل:'])
            ->add('tel', TextType::class,['label'=>'تلفن تماس:'])
            ->add('des', TextareaType::class,['label'=>'توضیحات بیشتر'])
            ->add('submit', SubmitType::class,['label'=>'ذخیره تغییرات'])
            ->getForm();
        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->update($hotel);
            $logMGR->addEvent('HOTLING' . $hotel->getId() . 'ROOM'.$hotel->getId(),'ویرایش','اطلاعات هتل','HOTELING',$request->getClientIp());
            return $this->redirectToRoute('hotelingHotelList', ['msg' => 3]);
        }
        return $this->render('hoteling/OPThotelEdit.html.twig', [
            'alerts'=>$alert,
            'form' => $form->createView(),
            'hotel'=>$hotel
        ]);
    }

    /**
     * @Route("/hoteling/opt/rooms/list/{id}/{msg}", name="hotelingRoomsList")
     */
    public function hotelingRoomsList($id,$msg = 0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $hotel = $entityMGR->find('App:hotelingHotel',$id);
        if(is_null($hotel))
            return $this->redirectToRoute('404');

        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'اتاق با موفقیت افزوده شد.']);
        elseif($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'اتاق با موفقیت ویرایش شد.']);

        return $this->render('hoteling/OPTrooms.html.twig',
            [
                'rooms'=>$entityMGR->findBy('App:HotelingRoom',['hotel'=>$hotel]),
                'alerts'=>$alerts,
                'hotel'=>$hotel
            ]);
    }

    /**
     * @Route("/hoteling/addpassenger/{id}/{msg}", name="hotelingAddPassenger")
     */
    public function hotelingAddPassenger($id,$msg = 0,EntityManagerInterface $entityManager,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $hotel = $entityMGR->find('App:hotelingHotel',$id);
        if(is_null($hotel))
            return $this->redirectToRoute('404');

        return $this->render('hoteling/OPTRES.html.twig',
            [
                'hotel'=>$hotel,
                'passengers'=>$entityMGR->findAll('App:CMPassenger'),
                'rooms' => $entityMGR->findBy('App:hotelingRoom', ['hotel' => $hotel]),
                'reqs' => $entityManager->createQueryBuilder('a')
                    ->select('a')
                    ->from('App:HotelingPassenger','a')
                    ->where('a.hotel = :hotel')
                    ->andWhere('a.dateSubmit >= :tmr')
                    ->setParameters(['hotel'=> $hotel,'tmr'=>time()-84600])
                    ->orderBy('a.id', 'DESC')
                    ->getQuery()
                    ->getResult()
            ]);
    }

    /**
     * @Route("/hoteling/newreqopt/{hid}/{rid}/{day}/{pid}", name="hotelingOptReserveNewRequest", options={"expose" = true})
     */
    public function hotelingOptReserveNewRequest($hid,$rid,$day,$pid,Service\Jdate $jdate,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ceremonialHotelingOPT','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $hotel = $entityMGR->find('App:HotelingHotel',$hid);

        $room = $entityMGR->find('App:HotelingRoom',$rid);

        $passenger = $entityMGR->findOneBy('App:CMPassenger',['pcodemeli'=>$pid]);

        $time = 86400;
        for($i=1; $i<=$day;$i++){
            $exist = $entityMGR->findOneBy('App:HotelingPassenger',[
                'hotel'=>$hotel,
                'room'=>$room,
                'passenger'=>$passenger,
                'day'=> $jdate->jdate('Y/m/d',time() + ($time * ($i-1)))
            ]);
            if(is_null($exist)){
                $res = new Entity\HotelingPassenger();
                $res->setPassenger($passenger);
                $res->setRoom($room);
                $res->setSubmiter($userMGR->currentPosition());
                $res->setDateSubmit(time() + ($time * ($i-1)));
                $res->setDay($jdate->jdate('Y/m/d',time() + ($time * ($i-1))));
                $res->setArea($userMGR->currentPosition()->getDefaultArea());
                $res->setHotel($hotel);
                $entityMGR->insertEntity($res);
            }

        }
        return new Response('ok');
    }

    /**
     * @Route("/hoteling/deletereserve/{id}}", name="hotelingDeleteReserve", options={"expose" = true})
     */
    public function hotelingDeleteReserve($id,Service\Jdate $jdate,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if (!$userMGR->hasPermission('ceremonialHotelingOPT', 'CEREMONIAL', null, $userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $res = $entityMGR->find('App:HotelingPassenger',$id);
        if(is_null($res))
            return new Response('nf');
        $entityMGR->remove('App:HotelingPassenger',$id);
        return new Response('ok');
    }
}
