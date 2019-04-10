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

class IctController extends AbstractController
{
    /**
     * @Route("/ictdoing/dashboard", name="ictDoingDashboard")
     */
    public function ictDoingDashboard(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ictDoing','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        return $this->render('ict/dashboardDoing.html.twig', [
            'reqCount'=> count($entityMGR->findBy('App:ICTRequest',['AcceptDoing'=>null,'areaID'=>$userMGR->currentPosition()->getDefaultArea()])),
            'reqArchiveCount' => count($entityMGR->findBy('App:ICTRequest',['AcceptDoing'=>'1','areaID'=>$userMGR->currentPosition()->getDefaultArea()])),
            'DeviceCount'=> count($entityMGR->findBy('App:ICTMachine',['areaID'=>$userMGR->currentPosition()->getDefaultArea()]))
        ]);
    }

    /**
     * @Route("/ictdoing/archive/{msg}", name="ictDoingArchive")
     */
    public function ictDoingArchive($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {

        if(! $userMGR->hasPermission('ictDoing','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $alerts = null;
        if($msg == 1)
            $alerts = [['type'=>'success','message'=>'درخواست شما با موفقیت ثبت شد.به زودی درخواست شما بررسی خواهد شد.']];
        $requests = $entityMGR->findBy('App:ICTRequest',[
            'areaID'=>$userMGR->currentPosition()->getDefaultArea(),
            'AcceptDoing'=>'1'
        ],[
            'id'=>'DESC'
        ]);
        foreach ($requests as $request)
            $request->setSubmitter($entityMGR->find('App:SysPosition',$request->getSubmitter())->getPublicLabel());
        return $this->render('ict/requestsArchiveDoing.html.twig', [
            'requests' => $requests,
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/ictdoing/activeworklist/{msg}", name="ictDoingActiveRequests")
     */
    public function ictDoingActiveRequests($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {

        if(! $userMGR->hasPermission('ictDoing','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $alerts = null;
        if($msg == 1)
            $alerts = [['type'=>'success','message'=>'درخواست شما با موفقیت ثبت شد.به زودی درخواست شما بررسی خواهد شد.']];
        $requests = $entityMGR->findBy('App:ICTRequest',[
            'areaID'=>$userMGR->currentPosition()->getDefaultArea(),
            'AcceptDoing'=>null
        ],[
            'id'=>'DESC'
        ]);
        foreach ($requests as $request)
            $request->setSubmitter($entityMGR->find('App:SysPosition',$request->getSubmitter())->getPublicLabel());
        return $this->render('ict/requestsArchiveDoing.html.twig', [
            'requests' => $requests,
            'alerts'=>$alerts
        ]);
    }
    /**
     * @Route("/ictdoing/requests/view/{rid}", name="ictdoingView")
     */
    public function ictdoingView($rid,Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('ictDoing','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        if(is_null($entityMGR->find('App:ICTRequest',$rid)))
            return $this->redirectToRoute('404');
        $default = ['message'=>'simple form'];
        $form = $this->createFormBuilder($default)
            ->add('requestType', EntityType::class,
                [
                    'label'=>'وضعیت درخواست',
                    'class'=>Entity\ICTRequestState::class,
                    'choice_value'=>'stateName',
                    'choice_label'=>'stateName'
                ]
            )
            ->add('des', TextareaType::class,['label'=>'شرح اقدام','required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        $req = $entityMGR->find('App:ICTRequest',$rid);
        if(is_null($req->getSeenTime()))
        {
            $req->setSeenTime(time());
            $req->setSeenID($userMGR->currentPosition()->getId());
            $entityMGR->update($req);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $doing = new Entity\ICTDoing();
            $doing->setDateSubmit(time());
            $doing->setSubmitter($userMGR->currentPosition()->getId());
            $doing->setReqID($rid);
            $doing->setDes($form->get('des')->getData());
            $entityMGR->insertEntity($doing);
            $req->setState($form->get('requestType')->getData()->getStateName());

            $entityMGR->update($req);
            $logger->info('position with username ' . $userMGR->currentUser()->getUsername() . ' submit new ICT Doing.' );
            $alert = [['type'=>'success','message'=>'اقدام با موفقیت ثبت شد.']];
        }

        $replays = $entityMGR->findBy('App:ICTDoing',[
            'reqID'=>$rid,
        ],[
            'id'=>'DESC'
        ]);
        foreach ($replays as $replay)
            $replay->setSubmitter($entityMGR->find('App:SysPosition',$replay->getSubmitter())->getPublicLabel());
        $req->setMachineID($entityMGR->find('App:ICTMachine',$req->getMachineID())->getMachineName());

        return $this->render('ict/requestViewDoing.html.twig', [
            'request' => $req,
            'replays' => $replays,
            'form'=>$form->createView(),
            'alerts'=>$alert
        ]);
    }

    /**
     * @Route("/ictreq/dashboard", name="ictreqDashboard")
     */
    public function ictreqDashboard(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ictRequest','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        return $this->render('ict/dashboard.html.twig', [
            'reqCount'=> count($entityMGR->findBy('App:ICTRequest',['submitter'=>$userMGR->currentPosition()->getId()]))
        ]);
    }

    /**
     * @Route("/ictreq/new/req", name="ictreqNew")
     */
    public function ictreqNew(Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('ictRequest','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $default = ['message'=>'simple form'];
        $form = $this->createFormBuilder($default)
            ->add('requestType', EntityType::class,
                [
                    'label'=>'نوع درخواست',
                    'class'=>Entity\ICTRequestType::class,
                    'choice_value'=>'typeName',
                    'choice_label'=>'typeName'
                    ]
            )
            ->add('EMSstate', EntityType::class,
                [
                    'label'=>'اولویت درخواست',
                    'class'=>Entity\ICTRequestEMSState::class,
                    'choice_value'=>'stateName',
                    'choice_label'=>'stateName'
                ]
            )
            ->add('machine', EntityType::class,
                [
                    'label'=>'دستگاه (رایانه, پرینتر و....)',
                    'class'=>Entity\ICTMachine::class,
                    'choice_value'=>'id',
                    'choice_label'=>'machineName'
                ]
            )
            ->add('des', TextareaType::class,['label'=>'شرح درخواست'])
            ->add('submit', SubmitType::class,['label'=>'ثبت درخواست'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $req = new Entity\ICTRequest();
            $req->setDes($form->get('des')->getData());
            $req->setDateSubmit(time());
            $req->setRequestType($form->get('requestType')->getData()->getTypeName());
            $req->setMachineID($form->get('machine')->getData()->getId());
            $req->setEMS($form->get('EMSstate')->getData()->getStateName());
            $req->setSubmitter($userMGR->currentPosition()->getId());
            $req->setAreaID($userMGR->currentPosition()->getDefaultArea());
            $req->setState('در حال بررسی');
            $entityMGR->insertEntity($req);
            $logger->info('position with username ' . $userMGR->currentUser()->getUsername() . ' submit new ICT request.' );
            return $this->redirectToRoute('ictreqArchive',['msg'=>'1']);
        }

        return $this->render('ict/newRequest.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ictreq/requests/archive/{msg}", name="ictreqArchive")
     */
    public function ictreqArchive($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {

        if(! $userMGR->hasPermission('ictRequest','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $alerts = null;
        if($msg == 1)
            $alerts = [['type'=>'success','message'=>'درخواست شما با موفقیت ثبت شد.به زودی درخواست شما بررسی خواهد شد.']];

        return $this->render('ict/requestsArchive.html.twig', [
            'requests' => $entityMGR->findBy('App:ICTRequest',[
                'areaID'=>$userMGR->currentPosition()->getDefaultArea(),
                'submitter'=>$userMGR->currentPosition()->getId()
            ],[
                'id'=>'DESC'
            ]),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/ictreq/requests/view/{rid}", name="ictreqView")
     */
    public function ictreqView($rid, Request $req ,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('ictRequest','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        if(is_null($entityMGR->find('App:ICTRequest',$rid)))
            return $this->redirectToRoute('404');
        $request = $entityMGR->find('App:ICTRequest',$rid);
        if($request->getSubmitter() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');
        $replays = $entityMGR->findBy('App:ICTDoing',[
            'reqID'=>$rid,
        ],[
            'id'=>'DESC'
        ]);
        foreach ($replays as $replay)
        {
            $replay->setSubmitter($entityMGR->find('App:SysPosition',$replay->getSubmitter())->getPublicLabel());
        }
        $default = ['message'=>'simple form'];
        $form = $this->createFormBuilder($default)
            ->add('submit', SubmitType::class,['attr'=>['class'=>'btn-success'],'label'=>'در صورت تایید دریافت خدمت مورد اشاره اینجا کلیک کنید'])
            ->getForm();
        $form->handleRequest($req);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $request->setAcceptDoing(1);
            $request->setAcceptDoingTime(time());
            $entityMGR->update($request);
            $alert=[['type'=>'success','message'=>'تایید اقدامات با موفقیت انجام شد.']];
        }

        $request->setMachineID($entityMGR->find('App:ICTMachine',$request->getMachineID())->getMachineName());


        return $this->render('ict/requestView.html.twig', [
            'request' => $request,
            'replays' => $replays,
            'form'=>$form->createView(),
            'alerts'=>$alert
        ]);
    }

    /**
     * @Route("/ictreq/new/device", name="ictDoingNewDevice")
     */
    public function ictDoingNewDevice(Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('ictDoing','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $default = ['message'=>'simple form'];
        $form = $this->createFormBuilder($default)
            ->add('ownerID', Type\AutocompleteType::class,['label'=>'تحویل گیرنده','attr'=>['pattern'=>'positions']])
            ->add('PCBrand', TextType::class,['label'=>'برند'])
            ->add('ProdectCode', TextType::class,['label'=>'کد اموال'])
            ->add('PCName', TextType::class,['label'=>'نام نرم افزاری رایانه'])
            ->add('PCCpu', EntityType::class,
                [
                    'label'=>'پردازنده',
                    'class'=>Entity\ICTCpuType::class,
                    'choice_value'=>'typeName',
                    'choice_label'=>'typeName'
                ]
            )
            ->add('PCRam', EntityType::class,
                [
                    'label'=>'رم',
                    'class'=>Entity\ICTRamType::class,
                    'choice_value'=>'typeName',
                    'choice_label'=>'typeName'
                ]
            )
            ->add('PCMainboard', TextType::class,['label'=>'برد اصلی'])
            ->add('PCHard', TextType::class,['label'=>'هارد دیسک'])
            ->add('des', TextareaType::class,['required'=>false,'label'=>'توضیحات بیشتر'])
            ->add('submit', SubmitType::class,['label'=>'ثبت دستگاه'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $position = $entityMGR->find('App:SysPosition',$form->get('ownerID')->getData());
            if(is_null($position))
            {
                $alerts = [['message'=>'سمت شغلی یافت نشد.','type'=>'danger']];
            }
            else{
                $machine = new Entity\ICTMachine();
                $machine->setOwnerID($position->getId());
                $machine->setDeviceType('رایانه ثابت / همراه');
                $machine->setAreaID($position->getDefaultArea());
                $machine->setDes($form->get('des')->getData());
                $machine->setPCBrand($form->get('PCBrand')->getData());
                $machine->setPCCpu($form->get('PCCpu')->getData()->getTypeName());
                $machine->setPCHard($form->get('PCHard')->getData());
                $machine->setPCMainboard($form->get('PCMainboard')->getData());
                $machine->setPCName($form->get('PCName')->getData());
                $machine->setProdectCode($form->get('ProdectCode')->getData());
                $machine->setMachineName('رایانه ثابت / همراه' . 'PC Name:' . $machine->getPCName() . ' کد اموال: ' . $machine->getProdectCode());
                $entityMGR->insertEntity($machine);
                $logger->info(sprintf('user %s add new device with id: %s',$userMGR->currentUser()->getUsername(),$machine->getId()));
                return $this->redirectToRoute('ictDoingDashboard',['msg'=>'1']);

            }
        }

        return $this->render('ict/newDevice.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ictDoing/list/devices/{msg}", name="ictDoingListDevices")
     */
    public function ictDoingListDevices($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {

        if(! $userMGR->hasPermission('ictDoing','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $alerts = null;
        if($msg == 1)
            $alerts = [['type'=>'success','message'=>'دستگاه با موفقیت اضافه شد.']];

        $machines = $entityMGR->findBy('App:ICTMachine',[
            'areaID'=>$userMGR->currentPosition()->getDefaultArea()
        ],[
            'id'=>'DESC'
        ]);
        foreach ($machines as $machine)
            $machine->setOwnerID($entityMGR->find('App:SysPosition',$machine->getOwnerID())->getPublicLabel());
        return $this->render('ict/listDevices.html.twig', [
            'machines' => $machines,
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/ictDoing/view/device/{id}", name="ictDoingViewDevice")
     */
    public function ictDoingViewDevice($id = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {

        if(! $userMGR->hasPermission('ictDoing','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $machine = $entityMGR->find('App:ICTMachine',$id);

        if(is_null($machine))
            return $this->redirectToRoute('404');
        if($machine->getAreaID() != $userMGR->currentPosition()->getDefaultArea())
            return $this->redirectToRoute('404');

        $machine->setOwnerID($entityMGR->find('App:SysPosition',$machine->getOwnerID())->getPublicLabel());

        return $this->render('ict/viewDevice.html.twig', [
            'machine' => $machine
        ]);
    }


}
