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
     * @Route("/hrm/report/user/io", name="HRMReportUserIO")
     */
    public function HRMReportUserIO(Request $request,Service\MssqlMGR $mssqlMGR,Service\ConfigMGR $configMGR,Service\UserMGR $userMGR,Service\Jdate $jdate)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');
        $siteConfig = $configMGR->getConfig();
        $mssqlMGR->configure($siteConfig->getHRMPWSERVERNAME(),$siteConfig->getHRMPWDATABASE(),$siteConfig->getHRMPWUSERNAME(),$siteConfig->getHRMPWPASSWORD());
        //get years from SG SERVER
        $conn = $mssqlMGR->getConnection();
        if(is_null($conn))
            return $this->redirectToRoute('500');


        $selectQuery1 = "SELECT * FROM DataFile WHERE (Emp_No = ?) ORDER BY Date ASC";
        $stmt = $conn->prepare($selectQuery1);
        $stmt->bindValue(1, $userMGR->currentUser()->getEmployeNum());
        $stmt->execute();
        $ioData = $stmt->fetchAll();

        //create Date Array
        $daySec = 24 * 60 * 60;
        $days =[];
        for($i = 31;$i > 0; $i --){
            $dayStr = $jdate->jdate('Ymd',time() - ($i * $daySec));
            $temp =[];
            foreach ($ioData as $io){
                if($io['Date'] == $dayStr)
                    array_push($temp,$io);
            }
            $days[$jdate->jdate('Y/m/d',time() - ($i * $daySec))] = $temp;
        }
        return $this->render('hrm/employe/io.html.twig', [
            'days'  =>  $days
        ]);
    }


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
        $yesrs = $stmt->fetchAll();
        $yearsArray = [];
        foreach ($yesrs as $yesr){
            $yearsArray['13' . $yesr['FYear'] ]= '13' . $yesr['FYear'];
        }
        $combs = ['message'=>'test'];
        $form = $this->createFormBuilder($combs)
            ->add('monthNames', EntityType::class, [
                'class'=>Entity\StaticMonth::class,
                'choice_label'=>'label',
                'choice_value' => 'code',
                'label'=>'ماه'
            ])
            ->add('years', ChoiceType::class, [
                'choices'=>$yearsArray,
                'label'=>'ماه'
            ])
            ->add('submit', SubmitType::class,['label'=>'جستوجوی فیش حقوقی'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        $fish = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $selectQuery1 = "SELECT * FROM tbuser WHERE (nationalCode = ?)";
            $stmt = $conn->prepare($selectQuery1);
            $stmt->bindValue(1, $userMGR->currentUser()->getNationalCode());
            $stmt->execute();
            $userInExternalDb = $stmt->fetch();
            $selectQuery1 =  "SELECT HRM.element.title,HRM.element.type, elmntref,val,issueyear,issuemonth,EffectYear,EffectMonth 
                            FROM HRM.element,HRM.payMVPers 
                            WHERE HRM.element.serial=HRM.payMVPers.elmntref AND val<>0 AND persref=? AND  issueyear=? and issuemonth=? ";
            $stmt = $conn->prepare($selectQuery1);
            $stmt->bindValue(1, $userInExternalDb['Serial']);
            $stmt->bindValue(2, $form->get('years')->getData());
            $stmt->bindValue(3, $form->get('monthNames')->getData()->getCode());
            $stmt->execute();
            $fish = $stmt->fetchAll();
            if(count($fish) == 0)
                $fish = 0;
        }
        return $this->render('hrm/reportEarn.html.twig', [
            'form' => $form->createView(),
            'hrmUser' =>$userMGR->currentUser(),
            'alert' =>$alerts,
            'fish'=>$fish
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
            'users' => $entityMGR->findBy('App:SysUser',['contractor'=>null])
        ]);
    }


}
