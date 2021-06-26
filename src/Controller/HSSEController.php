<?php

namespace App\Controller;

use App\Form\HSSEHealthEditType;
use App\Form\HSSEHealthType;
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

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Form\Type as Type;


use App\Service;
use App\Entity;


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
     * @Route("/hsse/persons/guid/submit/{id}", name="HSSEPersonsGuidSubmit")
     */
    public function HSSEPersonsGuidSubmit($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');

        return $this->render('hsse/persons.html.twig', [
            'users' => $entityMGR->findAll('App:CMPassenger'),
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
}
