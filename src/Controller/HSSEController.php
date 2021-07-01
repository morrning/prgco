<?php

namespace App\Controller;

use App\Form\HsseGuidSubmitType;
use App\Form\HSSEHealthEditType;
use App\Form\HSSEHealthType;
use App\Form\HsseHurtSubmitType;
use App\Form\HSSEPenaltyType;
use App\Form\HSSEToolType;
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
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Form\Type as Type;


use App\Service;
use App\Entity;
use Symfony\Component\Serializer\SerializerInterface;


class HSSEController extends AbstractController
{
    /**
     * @Route("/hsse/dashboard", name="HSSEDashboard")
     */
    public function HSSEDashboard(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if($userMGR->hasPermission('HSSEAREA','HSSE'))
        {
            return $this->render('hsse/HSEDashboard.html.twig', [
                'controller_name' => 'HSSEController',
            ]);
        }
        return $this->redirectToRoute('403');
    }
    /**
     * @Route("/hsse/persons/{msg}", name="HSSEPersons")
     */
    public function HSSEPersons($msg = 0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');

        return $this->render('hsse/persons.html.twig', [
            'users' => $entityMGR->findAll('App:CMPassenger'),
            'msg' => $msg
        ]);

    }

    /**
     * @Route("/hsse/persons/penalty/submit/{id}", name="HSSEPersonspenaltySubmit")
     */
    public function HSSEPersonspenaltySubmit($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $user = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($user))
            return $this->redirectToRoute('404');
        $penalty = new Entity\HssePenalty();
        $form = $this->createForm(HSSEPenaltyType::class,$penalty);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $penalty->setSubmiter($userMGR->currentPosition());
            $penalty->setDateSubmit(time());
            $penalty->setArea($userMGR->currentPosition()->getDefaultArea());
            $penalty->setPassenger($entityMGR->find('App:CMPassenger',$id));
            $entityMGR->insertEntity($penalty);
            return $this->redirectToRoute('HSSEPersons',['msg'=>1]);
        }
        return $this->render('hsse/penalty.html.twig', [
            'user' => $user,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/hsse/personslistpenalty/{msg}", name="HSSEPenaltyList")
     */
    public function HSSEPenaltyList($msg = 0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');

        return $this->render('hsse/listPenalty.html.twig', [
            'penaltys' => $entityMGR->findBy('App:HssePenalty',[
                'area'=>$userMGR->currentPosition()->getDefaultArea()
            ]),
        ]);

    }
    /**
     * @Route("/hsse/deletepenalty/{id}", name="HSSEDeletePenalty", options={"expose" = true})
     */
    public function HSSEDeletePenalty($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $entityMGR->remove('App:HssePenalty',$id);
        return new Response('ok');
    }

    /**
     * @Route("/hsse/viewepenalty/{id}", name="HSSEViewPenalty", options={"expose" = true})
     */
    public function HSSEViewPenalty($id,\Knp\Snappy\Pdf $knpSnappyPdf,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $penalty = $entityMGR->find('App:HssePenalty',$id);


        $html = $this->renderView('hsse/viewPenalty.html.twig',[
            'penalty' => $penalty
        ]);
        $knpSnappyPdf->setOption('page-size','A5');
        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'file.pdf','application/pdf','inline'
        );

    }
    /**
     * @Route("/hsse/persons/tools/submit/{id}", name="HSSEPersonsToolsSubmit")
     */
    public function HSSEPersonsToolsSubmit($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $user = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($user))
            return $this->redirectToRoute('404');

        $tool = new Entity\HsseTool();
        $tool->setNum(1);
        $form = $this->createForm(HSSEToolType::class,$tool);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $tool->setPassenger($user);
            $tool->setSubmitter($userMGR->currentPosition());
            $tool->setDateSubmit(time());
            $tool->setArea($userMGR->currentPosition()->getDefaultArea());
            $entityMGR->insertEntity($tool);
            return $this->redirectToRoute('HSSEPersons',['msg'=>2]);
        }

        return $this->render('hsse/personsToolSubmit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'items' => $entityMGR->findBy('App:HsseTool',['passenger'=>$user])
        ]);
    }

    /**
     * @Route("/hsse/deleteToolSubmit/{id}", name="HSSEDeleteToolSubmit", options={"expose" = true})
     */
    public function HSSEDeleteToolSubmit($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $entityMGR->remove('App:HsseTool',$id);
        return new Response('ok');
    }

    /**
     * @Route("/hsse/personslistTools/{msg}", name="HSSEToolsList")
     */
    public function HSSEToolsList($msg = 0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');

        return $this->render('hsse/listTools.html.twig', [
            'items' => $entityMGR->findBy('App:HsseTool',[
                'area'=>$userMGR->currentPosition()->getDefaultArea()
            ]),
        ]);
    }

    /**
     * @Route("/hsse/persons/health/submit/{id}", name="HSSEPersonHealthSubmit")
     */
    public function HSSEPersonHealthSubmit($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $user = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($user))
            return $this->redirectToRoute('404');
        $health = new Entity\HsseHealth();
        $form = $this->createForm(HSSEHealthType::class,$health);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $health->setDateSubmit(time());
            $health->setSubmitter($userMGR->currentPosition());
            $health->setArea($userMGR->currentPosition()->getDefaultArea());
            $health->setPassenger($entityMGR->find('App:CMPassenger',$id));
            $entityMGR->insertEntity($health);
            return $this->redirectToRoute('HSSEHealthList',['msg'=>1]);
        }
        return $this->render('hsse/health.html.twig', [
            'user' => $user,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/hsse/persons/health/edit/{id}/{msg}", name="HSSEPersonHealthEdit")
     */
    public function HSSEPersonHealthEdit($id,$msg = 0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $health = $entityMGR->find('App:HsseHealth',$id);
        if(is_null($health))
            return $this->redirectToRoute('404');
        $form = $this->createForm(HSSEHealthEditType::class,$health);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityMGR->insertEntity($health);
            return $this->redirectToRoute('HSSEHealthList',['msg'=>2]);
        }
        return $this->render('hsse/healthEdit.html.twig', [
            'user' => $health->getPassenger(),
            'form'=>$form->createView(),
            'health' => $health,
            'msg' => $msg
        ]);
    }

    /**
     * @Route("/hsse/persons/health/outSubmit/{id}", name="HSSEPersonHealthEditoutSubmit")
     */
    public function HSSEPersonHealthEditoutSubmit($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $health = $entityMGR->find('App:HsseHealth',$id);
        if(is_null($health))
            return $this->redirectToRoute('404');
        $health->setDateOut(time());
        $entityMGR->update($health);
        return $this->redirectToRoute('HSSEPersonHealthEdit',['id'=>$health->getId(),'msg'=>1]);
    }

    /**
     * @Route("/hsse/personslistHealth/{msg}", name="HSSEHealthList")
     */
    public function HSSEHealthList($msg = 0,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');

        return $this->render('hsse/listHealth.html.twig', [
            'items' => $entityMGR->findBy('App:HsseHealth',[
                'area'=>$userMGR->currentPosition()->getDefaultArea()
            ]),
            'msg'=>$msg
        ]);
    }

    /**
     * @Route("/hsse/persons/folder/{id}", name="HSSEPersonfolder")
     */
    public function HSSEPersonfolder($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($passenger))
            return $this->redirectToRoute('404');
        $healths = $entityMGR->findBy('App:HsseHealth',['passenger'=>$passenger,'area'=>$userMGR->currentPosition()->getDefaultArea()]);
        $tools = $entityMGR->findBy('App:HsseTool',['passenger'=>$passenger,'area'=>$userMGR->currentPosition()->getDefaultArea()]);
        $penaltys = $entityMGR->findBy('App:HssePenalty',['Passenger'=>$passenger,'area'=>$userMGR->currentPosition()->getDefaultArea()]);
        //get guids
        $cmLists = $entityMGR->findBy('App:CMListUser',['cmpassenger'=>$passenger]);
        $guidLists = [];
        foreach ($cmLists as $cmList){
            $guid = $entityMGR->findOneBy('App:HsseGuid',['area'=>$userMGR->currentPosition()->getDefaultArea(),'cmlist'=>$cmList->getCmlist()]);
            if(!is_null($guid))
                array_push($guidLists,$guid);
        }

        return $this->render('hsse/personFolder.html.twig',[
            'penaltys'=>$penaltys,
            'tools'=>$tools,
            'healths'=>$healths,
            'passenger'=>$passenger,
            'guids' => $guidLists
        ]);
    }

    /**
     * @Route("/hsse/persons/guid/submit/{id}", name="HSSEPersonsGuidSubmit")
     */
    public function HSSEPersonsGuidSubmit($id = 0, Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $list = new Entity\CMList();
        $list->setSubmitter($userMGR->currentPosition());
        $list->setListLabel('لیست آموزش ایمنی');
        $list->setDes('لیست آموزش ایمنی');
        $entityMGR->insertEntity($list);
        $plList = [];
        if($id != 0){
            $passenger = $entityMGR->find('App:CMPassenger',$id);
            if(!is_null($passenger)){
                $pl = new Entity\CMListUser();
                $pl->setCmpassenger($passenger);
                $pl->setCmlist($list);
                $entityMGR->insertEntity($pl);
                array_push($plList,$pl);
            }

        }
        return $this->render('hsse/guidSubmit.html.twig', [
            'passengers' => $entityMGR->findAll('App:CMPassenger'),
            'activeListID' => $list->getId(),
            'pls' => $plList
        ]);
    }

    /**
     * @Route("/hsse/cmlist/existpassenger/{lid}/{pid}", name="hsseCMLISTexistPassenger", options={"expose" = true})
     */
    public function hsseCMLISTexistPassenger($lid,$pid,Request $request, Service\EntityMGR $entityMGR, Service\UserMGR $userMGR, LoggerInterface $logger,Service\LogMGR $logMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$pid);
        if(is_null($passenger))
            return new Response('er');
        $list = $entityMGR->find('App:CMList',$lid);
        if(is_null($list))
            return new Response('er');
        $lu = $entityMGR->findOneBy('App:CMListUser',['cmlist'=>$list,'cmpassenger'=>$passenger]);
        if(is_null($lu))
            return new Response('nf');
        return new Response('exist');
    }

    /**
     * @Route("/hsse/cmlist/addpassenger/{lid}/{pid}", name="hsseCMLISTaddPassenger", options={"expose" = true})
     */
    public function hsseCMLISTaddPassenger($lid,$pid,SerializerInterface $serializer,Request $request, Service\EntityMGR $entityMGR, Service\UserMGR $userMGR, LoggerInterface $logger,Service\LogMGR $logMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$pid);
        if(is_null($passenger))
            return new Response('er');
        $list = $entityMGR->find('App:CMList',$lid);
        if(is_null($list))
            return new Response('er');
        $lu = $entityMGR->findOneBy('App:CMListUser',['cmlist'=>$list,'cmpassenger'=>$passenger]);
        if(is_null($lu)){
            $lp = new Entity\CMListUser();
            $lp->setCmlist($list);
            $lp->setCmpassenger($passenger);
            $entityMGR->insertEntity($lp);
            $resp = [];
            $resp['id'] = $lp->getId();
            $resp['name'] = $passenger->getPname() . ' ' . $passenger->getPfamily();
            $resp['father'] = $passenger->getPfather();
            $resp['codemeli'] = $passenger->getPcodemeli();
            $resp['upper'] = $passenger->getSubmitter()->getPublicLabel();
            return new Response($serializer->serialize($resp, 'json'));
        }
        return new Response('exist');
    }

    /**
     * @Route("/hsse/cmlist/removepassenger/{id}", name="hsseCMLISTremovePassenger", options={"expose" = true})
     */
    public function hsseCMLISTremovePassenger($id,SerializerInterface $serializer,Request $request, Service\EntityMGR $entityMGR, Service\UserMGR $userMGR, LoggerInterface $logger,Service\LogMGR $logMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $entityMGR->remove('App:CMListUser',$id);
        return new Response('ok');
    }

    /**
     * @Route("/hsse/cmlist/hasmember/{id}", name="hsseCMLISThasmember", options={"expose" = true})
     */
    public function hsseCMLISThasmember($id,SerializerInterface $serializer,Request $request, Service\EntityMGR $entityMGR, Service\UserMGR $userMGR, LoggerInterface $logger,Service\LogMGR $logMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $list = $entityMGR->find('App:CMList',$id);
        if(!is_null($list)){
            $result = $entityMGR->findBy('App:CMListUser',['cmlist'=>$list]);
            if(count($result) != 0)
                return new Response('ok');
        }
        return new Response('null');
    }

    /**
     * @Route("/hsse/persons/guid/create/{id}", name="HSSEPersonsGuidCreate", options={"expose" = true})
     */
    public function HSSEPersonsGuidCreate($id, Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');

        $list = $entityMGR->find('App:CMList',$id);
        if(is_null($list))
            return $this->redirectToRoute('404');

        $guid = new Entity\HsseGuid();
        $form = $this->createForm(HsseGuidSubmitType::class,$guid);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $guid->setCmlist($list);
            $guid->setSubmitter($userMGR->currentPosition());
            $guid->setDateSubmit(time());
            $guid->setArea($userMGR->currentPosition()->getDefaultArea());
            $entityMGR->insertEntity($guid);
            return $this->redirectToRoute('HSSEPersonsGuidList',['msg'=>1]);
        }

        return $this->render('hsse/guidCreate.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/hsse/persons/guid/list/{msg}", name="HSSEPersonsGuidList", options={"expose" = true})
     */
    public function HSSEPersonsGuidList($msg=0, Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');

        return $this->render('hsse/guidsList.html.twig',[
            'items'=>$entityMGR->findBy('App:HsseGuid',['area'=>$userMGR->currentPosition()->getDefaultArea()])
        ]);
    }

    /**
     * @Route("/hsse/persons/guid/view/{id}/{msg}", name="HSSEPersonsGuidView", options={"expose" = true})
     */
    public function HSSEPersonsGuidView($id,$msg=0, Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $guid = $entityMGR->find('App:HsseGuid',$id);
        if(is_null($guid))
            return $this->redirectToRoute('404');

        return $this->render('hsse/guidView.html.twig',[
            'guid' => $guid,
            'passengers'=>$entityMGR->findBy('App:CMListUser',['cmlist'=>$guid->getCmlist()])
        ]);
    }

    /**
     * @Route("/hsse/hurt/create", name="HSSEHurtSubmit", options={"expose" = true})
     */
    public function HSSEHurtSubmit(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $hurt = new Entity\HsseHurt();
        $form = $this->createForm(HsseHurtSubmitType::class,$hurt);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $list = new Entity\CMList();
            $list->setSubmitter($userMGR->currentPosition());
            $list->setListLabel('حوادث ایمنی');
            $list->setDes('حوادث ایمنی');
            $entityMGR->insertEntity($list);

            $hurt->setCmlist($list);
            $hurt->setSubmitter($userMGR->currentPosition());
            $hurt->setDateSubmit(time());
            $hurt->setArea($userMGR->currentPosition()->getDefaultArea());
            $entityMGR->insertEntity($hurt);
            return $this->redirectToRoute('HSSEHurtView',['id'=>$hurt->getId(),'msg'=>1]);
        }

        return $this->render('hsse/hurtCreate.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/hsse/hurt/view/{id}/{msg}", name="HSSEHurtView", options={"expose" = true})
     */
    public function HSSEHurtView($id,$msg=0, Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $hurt = $entityMGR->find('App:HsseHurt',$id);
        if(is_null($hurt))
            return $this->redirectToRoute('404');

        return $this->render('hsse/HurtView.html.twig',[
            'hurt' => $hurt,
            'passengersList'=>$entityMGR->findBy('App:CMListUser',['cmlist'=>$hurt->getCmlist()]),
            'passengers' => $entityMGR->findAll('App:CMPassenger'),
        ]);
    }

    /**
     * @Route("/hsse/persons/hurt/list/{msg}", name="HSSEPersonsHurtList", options={"expose" = true})
     */
    public function HSSEPersonsHurtList($msg=0, Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');

        return $this->render('hsse/hurtList.html.twig',[
            'items'=>$entityMGR->findBy('App:HsseHurt',['area'=>$userMGR->currentPosition()->getDefaultArea()]),
        ]);
    }
}
