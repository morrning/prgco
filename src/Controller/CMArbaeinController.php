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
    public function cmarbainDashboard(Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,Service\Jdate $jdate)
    {
        if(! $userMGR->hasPermission('CMOPTARBAEIN','CMARBAEIN',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $today = $jdate->jdate('Y/n/d',time());
        return $this->render('cm_arbaein/dashboard.html.twig', [
            'area'=>$userMGR->currentPosition()->getDefaultArea(),
            'total'=>count($entityMGR->findBy('App:CMArbaein',['area'=>$userMGR->currentPosition()->getDefaultArea()])),
            'online'=>count($entityMGR->findBy('App:CMArbaein',['area'=>$userMGR->currentPosition()->getDefaultArea(),'outputDate'=>null])),
            'menCount'=>count($entityMGR->findBy('App:CMArbaein',['area'=>$userMGR->currentPosition()->getDefaultArea(),'outputDate'=>null,'isMan'=>true])),
            'womenCount'=>count($entityMGR->findBy('App:CMArbaein',['area'=>$userMGR->currentPosition()->getDefaultArea(),'outputDate'=>null,'isMan'=>false])),
            'inputToday'=>count($entityMGR->findBy('App:CMArbaein',['area'=>$userMGR->currentPosition()->getDefaultArea(),'inputDate'=>$today])),
            'outputToday'=>count($entityMGR->findBy('App:CMArbaein',['area'=>$userMGR->currentPosition()->getDefaultArea(),'outputDate'=>$today])),
        ]);
    }

    /**
     * @Route("/c/m/arbaein/list", name="cmarbainListZaer")
     */
    public function cmarbainListZaer(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CMOPTARBAEIN','CMARBAEIN',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        return $this->render('cm_arbaein/list.html.twig', [
            'zaers' =>$entityMGR->findBy('App:CMArbaein',['area'=>$userMGR->currentPosition()->getDefaultArea()])

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
            ->add('fullname', TextType::class,['label'=>'نام و نام خانوادگی:','required'=>false,'attr'=>['class'=>'autoClear']])
            ->add('birthday', TextType::class,['label'=>'سال تولد:','required'=>false, 'attr'=>['class'=>'autoClear']])
            ->add('tel', TextType::class,['label'=>'شماره تماس:','required'=>false,'attr'=>['class'=>'tel autoClear']])
            ->add('isMan', ChoiceType::class,[
                    'label'=>'جنسیت:',
                    'choices'=>['مرد'=>true,'زن'=>false],
                ])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(!is_null($entityMGR->findOneBy('App:CMArbaein',['FGUID'=>$form->get('FGUID')->getData()])))
                array_push($alerts,['type'=>'danger','message'=>'این فرم قبلا ثبت شده است.']);
            elseif(!is_null($entityMGR->findOneBy('App:CMArbaein',['CGUID'=>$form->get('CGUID')->getData()])))
                array_push($alerts,['type'=>'danger','message'=>'این کارت قبلا ثبت شده است.']);
            elseif(!is_null($entityMGR->findOneBy('App:CMArbaein',[
                'codemeli'=>$form->get('codemeli')->getData(),
                'outputDate'=>null
                ])))
                array_push($alerts,['type'=>'danger','message'=>'زائر با این کد ملی قبلا ثبت شده است.']);
            else{
                $zaer->setArea($userMGR->currentPosition()->getDefaultArea());
                $zaer->setInputDate($jdate->jdate('Y/n/d',time()));
                $zaer->setInputer($userMGR->currentPosition());
                $zaer->setCompleter($userMGR->currentPosition());
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
     * @Route("/c/m/arbaein/searchinfo/card/{msg}", name="cmarbainSearchInfoCard")
     */
    public function cmarbainSearchInfoCard($msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,Service\Jdate $jdate)
    {
        if(! $userMGR->hasPermission('CMOPTARBAEIN','CMARBAEIN',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $zaer = new Entity\CMArbaein();
        $form = $this->createFormBuilder($zaer)
            ->add('FGUID', TextType::class,['label'=>'کد فرم:','required'=>true,'attr'=>['class'=>'autoClear activeInput']])
            ->add('submit', SubmitType::class,['label'=>'جست و جو'])
            ->getForm();
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'اطلاعات زائر با موفقیت ثبت گردید.']);
        elseif($msg == 2)
            array_push($alerts,['type'=>'danger','message'=>'زائری با مشخصات وارد شده ثبت نشده است.']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(is_null($entityMGR->findOneBy('App:CMArbaein',['FGUID'=>$form->get('FGUID')->getData()])))
                return $this->redirectToRoute('cmarbainSearchInfoCard',['msg'=>2]);
            else
                return $this->redirectToRoute('cmarbainSCompleteCard',['id'=>$form->get('FGUID')->getData()]);
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
        $exist = $entityMGR->findOneBy('App:CMArbaein',['codemeli'=>$zaer->getCodemeli()]);
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
                ->add('tel', TextType::class,['data'=>$exist->getTel(),'label'=>'شماره تماس:','required'=>true,'attr'=>['class'=>'tel']])
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
            return $this->redirectToRoute('cmarbainSearchInfoCard',['msg'=>1]);
        }

        return $this->render('cm_arbaein/completeInfo.html.twig', [
            'form'=>$form->createView(),
            'alerts'=>$alerts,
            'zaer'=>$zaer
        ]);
    }

    /**
     * @Route("/c/m/arbaein/exit/zaer/{msg}", name="cmarbainExitZaer")
     */
    public function cmarbainExitZaer($msg=0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,Service\Jdate $jdate)
    {
        if(! $userMGR->hasPermission('CMOPTARBAEIN','CMARBAEIN',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $zaer = new Entity\CMArbaein();
        $form = $this->createFormBuilder($zaer)
            ->add('CGUID', TextType::class,['label'=>'کد کارت:','required'=>true,'attr'=>['class'=>'autoClear activeInput']])
            ->add('submit', SubmitType::class,['label'=>'جست و جو'])
            ->getForm();
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'خروج زائر با موفقیت ثبت شد.']);
        elseif($msg == 2)
            array_push($alerts,['type'=>'danger','message'=>'زائری با مشخصات وارد شده ثبت نشده است.']);
        elseif($msg == 3)
            array_push($alerts,['type'=>'danger','message'=>'زائر قبلا خارج و کارت باطل شده است.']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $zaerObj = $entityMGR->findOneBy('App:CMArbaein',['CGUID'=>$form->get('CGUID')->getData()]);
            if(is_null($zaerObj))
                return $this->redirectToRoute('cmarbainExitZaer',['msg'=>2]);
            else{
                if(is_null($zaerObj->getOutputDate())){
                    return $this->redirectToRoute('cmarbainExitZaerEND',['id'=>$zaerObj->getId()]);
                }
                else
                    return $this->redirectToRoute('cmarbainExitZaer',['msg'=>3]);
            }
        }
        return $this->render('cm_arbaein/searchExit.html.twig', [
            'form'=>$form->createView(),
            'alerts'=>$alerts,
        ]);

    }

    /**
     * @Route("/c/m/arbaein/exit/complete/exit/{id}", name="cmarbainExitZaerEND")
     */
    public function cmarbainExitZaerEND($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,Service\Jdate $jdate)
    {
        if(! $userMGR->hasPermission('CMOPTARBAEIN','CMARBAEIN',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $zaer = $entityMGR->find('App:CMArbaein',$id);
        if(is_null($zaer))
            return $this->redirectToRoute('404');

        $form = $this->createFormBuilder($zaer)
            ->add('submit', SubmitType::class,['label'=>'ثبت خروج و ابطال کارت'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $zaer->setOutputDate($jdate->jdate('Y/n/d',time()));
            $zaer->setOutputer($userMGR->currentPosition());
            $entityMGR->update($zaer);
            return $this->redirectToRoute('cmarbainExitZaer',['msg'=>1]);
        }
        return $this->render('cm_arbaein/exit.html.twig', [
            'form'=>$form->createView(),
            'alerts'=>$alerts,
            'zaer'=>$zaer
        ]);
    }

    /**
     * @Route("/c/m/arbaein/report", name="cmarbainReport")
     */
    public function cmarbainReport(Request $request,Service\Jdate $jdate,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CMOPTARBAEIN','CMARBAEIN',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $daily = [];
        $tenDaysInput=[];
        $today = $jdate->jdate('Y/n/d',time());
        $areaes = $entityMGR->findAll('App:SysArea');
        foreach ($areaes as $area){
            $areaRep = [];
            $areaRep['name'] = $area->getAreaName();
            $areaRep['countTodayInput'] = count($entityMGR->findBy('App:CMArbaein',['area'=>$area,'inputDate'=>$today]));
            $areaRep['countTodayOutput'] = count($entityMGR->findBy('App:CMArbaein',['area'=>$area,'outputDate'=>$today]));

            if($areaRep['countTodayInput'] != 0 || $areaRep['countTodayOutput'] != 0)
                array_push($daily,$areaRep);

            //10 day before
            $tenDayInput = [];
            $tenDayName = [];
            for($i=10;$i>=0;$i--)
            {
                $thatTime = explode('/',$today);
                $thatTime[2] = $thatTime[2] - $i;
                if($thatTime[2]<=0)
                {
                    if($thatTime[1]>6)
                        $thatTime[2] = 30;
                    else
                        $thatTime[2] = 31;

                    $thatTime[1] --;
                }
                $targetDate = implode('/',$thatTime);
                array_push($tenDayName,$targetDate);

                array_push($tenDayInput,count($entityMGR->findBy('App:CMArbaein',['area'=>$area->getId(),'inputDate'=>$targetDate])));

            }
            array_push($tenDaysInput,$tenDayInput);

        }
        return $this->render('cm_arbaein/report.html.twig', [
            'daylys' =>$daily,
            'pn'=> array_column($daily, 'name'),
            'pi'=> array_column($daily, 'countTodayInput'),
            'po'=> array_column($daily, 'countTodayOutput'),
            'tenDaysInput'=>$tenDaysInput,
            'tenDaysName'=>$tenDayName
        ]);
    }
}
