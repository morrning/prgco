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
            'reqCount'=> count($entityMGR->findBy('App:ICTRequest',['areaID'=>$userMGR->currentPosition()->getDefaultArea()]))
        ]);
    }

    /**
     * @Route("/ictdoing/archive/{msg}", name="ictDoingArchive")
     */
    public function ictDoingArchive($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {

        if(! $userMGR->hasPermission('ictRequest','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $alerts = null;
        if($msg == 1)
            $alerts = [['type'=>'success','message'=>'درخواست شما با موفقیت ثبت شد.به زودی درخواست شما بررسی خواهد شد.']];
        $requests = $entityMGR->findBy('App:ICTRequest',[
            'areaID'=>$userMGR->currentPosition()->getDefaultArea(),
            'submitter'=>$userMGR->currentPosition()->getId()
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
        if(! $userMGR->hasPermission('ictRequest','ICT',null,$userMGR->currentPosition()->getDefaultArea()))
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
    public function ictreqView($rid ,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
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
        return $this->render('ict/requestView.html.twig', [
            'request' => $request,
            'replays' => $replays
        ]);
    }

}
