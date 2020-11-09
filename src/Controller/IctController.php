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
            'reqArchiveCount' => count($entityMGR->findBy('App:ICTRequest',['AcceptDoing'=>'1','areaID'=>$userMGR->currentPosition()->getDefaultArea()]))
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
            $req->setSeenID($userMGR->currentPosition());
            $entityMGR->update($req);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $doing = new Entity\ICTDoing();
            $doing->setDateSubmit(time());
            $doing->setSubmitter($userMGR->currentPosition());
            $doing->setReqID($req);
            $doing->setDes($form->get('des')->getData());
            $entityMGR->insertEntity($doing);
            $req->setState($form->get('requestType')->getData());
            $entityMGR->update($req);
            $logger->info('position with username ' . $userMGR->currentUser()->getUsername() . ' submit new ICT Doing.' );
            $alert = [['type'=>'success','message'=>'اقدام با موفقیت ثبت شد.']];
            $des = sprintf('پاسخ درخواست خدمات توسط %s ارسال شد.',$userMGR->currentPosition()->getPublicLabel());
            $url = $this->generateUrl('ictreqView',['rid'=>$req->getId()]);
            $userMGR->addNotificationForUser($entityMGR->find('App:SysPosition',$req->getSubmitter()), $des,$url);

        }

        $replays = $entityMGR->findBy('App:ICTDoing',[
            'reqID'=>$req,
        ],[
            'id'=>'DESC'
        ]);

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
    public function ictreqNew(Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger,Service\LogMGR $logMGR)
    {
        if(! $userMGR->hasPermission('ictRequest','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $req = new Entity\ICTRequest();
        $form = $this->createFormBuilder($req)
            ->add('requestType', EntityType::class,
                [
                    'label'=>'نوع درخواست',
                    'class'=>Entity\ICTRequestType::class,
                    'choice_value'=>'typeName',
                    'choice_label'=>'typeName'
                    ]
            )
            ->add('ems', EntityType::class,
                [
                    'label'=>'اولویت درخواست',
                    'class'=>Entity\ICTRequestEMSState::class,
                    'choice_value'=>'stateName',
                    'choice_label'=>'stateName'
                ]
            )
            ->add('des', TextareaType::class,['label'=>'شرح درخواست'])
            ->add('submit', SubmitType::class,['label'=>'ثبت درخواست'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $req->setDateSubmit(time());
            $req->setSubmitter($userMGR->currentPosition());
            $req->setAreaID($userMGR->currentPosition()->getDefaultArea());
            $req->setState($entityMGR->findOneBy('App:ICTRequestState',['stateName'=>'در حال بررسی']));
            $entityMGR->insertEntity($req);
            $logger->info('position with username ' . $userMGR->currentUser()->getUsername() . ' submit new ICT request.' );
            $logMGR->addEvent('ICTREQ'.$userMGR->currentUser()->getId(),'افزودن','درخواست خدمات فناوری اطلاعات و ارتباطات','ICT',$request->getClientIp());
            $des = sprintf('درخواست خدمات توسط %s ثبت شد.',$userMGR->currentPosition()->getPublicLabel());
            $url = $this->generateUrl('ictdoingView',['rid'=>$req->getId()]);
            $userMGR->addNotificationForGroup('ictDoing','ICT',$des,$url,$userMGR->currentPosition()->getDefaultArea()->getId());
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
                'submitter'=>$userMGR->currentPosition()
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
        $rid = $entityMGR->find('App:ICTRequest',$rid);
        if(is_null($rid))
            return $this->redirectToRoute('404');
        $request = $entityMGR->find('App:ICTRequest',$rid);
        if($request->getSubmitter() != $userMGR->currentPosition())
            return $this->redirectToRoute('403');
        $replays = $entityMGR->findBy('App:ICTDoing',[
            'reqID'=>$rid,
        ],[
            'id'=>'DESC'
        ]);

        $default = ['message'=>'simple form'];
        $form = $this->createFormBuilder($default)
            ->add('submit', SubmitType::class,['attr'=>['class'=>'btn-success'],'label'=>'در صورت تایید دریافت خدمت مورد اشاره اینجا کلیک کنید'])
            ->getForm();
        $form->handleRequest($req);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $request->setAcceptDoing(1);
            $request->setAcceptDoingTime(time());
            $request->setState($entityMGR->findOneBy('App:ICTRequestState',['stateCode'=>2]));
            $entityMGR->update($request);
            $alert=[['type'=>'success','message'=>'تایید اقدامات با موفقیت انجام شد.']];
        }
        return $this->render('ict/requestView.html.twig', [
            'request' => $request,
            'replays' => $replays,
            'form'=>$form->createView(),
            'alerts'=>$alert
        ]);
    }



}
