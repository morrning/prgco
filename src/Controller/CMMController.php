<?php

namespace App\Controller;

use http\Client;
use Psr\Log\NullLogger;
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

class CMMController extends AbstractController
{
    /**
     * @Route("/ceremonial/mngtotal/list/passengers", name="ceremonialMNGTOTALPassengers")
     */
    public function ceremonialMNGTOTALPassengers(Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('FRE56','مشاهده','داشبورد مدیریت کل سامانه تشریفات','CEREMONIAL',$request->getClientIp());

        $passengers = $entityMGR->findAll('App:CMPassenger');
        return $this->render('cmm/cmmPassengers.html.twig', [
            'passengers'=>$passengers
        ]);
    }

}
