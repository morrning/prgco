<?php

namespace App\Controller;

use App\Form\HSSEPenaltyType;
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
// Include Dompdf required namespaces
use Dompdf\Dompdf;
use Dompdf\Options;

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
            'user' => $entityMGR->find('App:CMPassenger',$id),
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
    public function HSSEViewPenalty($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');
        $penalty = $entityMGR->find('App:HssePenalty',$id);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('DOMPDF_UNICODE_ENABLED',true);
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('hsse/viewPenalty.html.twig',[
            'penalty' => $penalty
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);

    }
    /**
     * @Route("/hsse/persons/tools/submit/{id}", name="HSSEPersonsToolsSubmit")
     */
    public function HSSEPersonsToolsSubmit($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');

        return $this->render('hsse/personsToolSubmit.html.twig', [
            'user' => $entityMGR->find('App:CMPassenger',$id),
        ]);
    }
}
