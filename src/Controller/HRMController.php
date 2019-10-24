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
class HRMController extends AbstractController
{

    /**
     * @Route("/hrm/report/earn", name="HRMReportEarn")
     */
    public function HRMReportEarn(Request $request,Service\MssqlMGR $mssqlMGR,Service\ConfigMGR $configMGR,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        $siteConfig = $configMGR->getConfig();
        $mssqlMGR->configure($siteConfig->getHRMSGSERVERNAME(),$siteConfig->getHRMSGDATABASE(),$siteConfig->getHRMSGUSERNAME(),$siteConfig->getHRMSGPASSWORD());
        //get years from SG SERVER
        $conn = $mssqlMGR->getConnection();
        if(is_null($conn))
            return $this->redirectToRoute('500');

        $str = 'Select FYear from GNR.GenYears ORDER BY FYear DESC';
        $stmt = $conn->query($str);
        echo count($stmt->fetchAll());
        $combs = ['message'=>'test'];
        $form = $this->createFormBuilder($combs)
            ->add('monthNames', EntityType::class, [
                'class'=>Entity\StaticMonth::class,
                'choice_label'=>'label',
                'choice_value' => 'code',
                'label'=>'ماه'
            ])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }
        return $this->render('hrm/reportEarn.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/hrm/admin", name="HRMadmin")
     */
    public function HRMadmin(Service\MssqlMGR $mssqlMgr,Service\ConfigMGR $configMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('HRMAREAACCESS','HRM',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $siteConfig = $configMGR->getConfig();
        $mssqlMgr->configure($siteConfig->getHRMSGSERVERNAME(),$siteConfig->getHRMSGDATABASE(),$siteConfig->getHRMSGUSERNAME(),$siteConfig->getHRMSGPASSWORD());
        return $this->render('hrm/dashboard.html.twig', [

        ]);
    }

    /**
     * @Route("/hrm/employe/list", name="HRMEmployelist")
     */
    public function employes(Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMAREAACCESS','HRM',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        return $this->render('hrm/employes.html.twig', [
            'employes' => $entityMGR->findBy('App:HRMemploye',['area'=>$userMGR->currentPosition()->getDefaultArea()]),
        ]);
    }

    /**
     * @Route("/hrm/add/employe/s1", name="HRMEmployeAddSTP1")
     */
    public function HRMEmployeAddSTP1(Request $request,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        if(! $userMGR->hasPermission('HRMAREAACCESS','HRM',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $employe = new Entity\HRMemploye();
        $form = $this->createFormBuilder($employe)
            ->add('nationalCode', TextType::class,['label'=>'کد ملی:','required'=>true,'attr'=>['class'=>'autoClear activeInput']])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }
        return $this->render('hrm/addEmploySTP1.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
