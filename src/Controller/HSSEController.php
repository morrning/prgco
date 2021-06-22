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
     * @Route("/hsse/persons", name="HSSEPersons")
     */
    public function HSSEPersons(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HSSEAREA','HSSE'))
            return $this->redirectToRoute('403');

        return $this->render('hsse/persons.html.twig', [
            'users' => $entityMGR->findAll('App:CMPassenger'),
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
