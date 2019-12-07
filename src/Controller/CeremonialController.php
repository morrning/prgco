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

class CeremonialController extends AbstractController
{

    /**
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 32) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * @Route("/ceremonial/req/acc/balance/{msg}", name="ceremonialACCBalance")
     */
    public function ceremonialACCBalance($msg=0,Request $request,Service\ACC $ACC,Service\LogMGR $logMGR, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $moneyLabels=[];
        $moneyTypes = $entityMGR->findAll('App:ACCMoney');
        $moneyTotal = [];

        if($ACC->hasAccount($userMGR->currentUser()))
            $account = $ACC->getAccountByUser($userMGR->currentUser());
        else
            $account = $ACC->addAccount($userMGR->currentUser()->getFullname(),$userMGR->currentUser());
        foreach ($moneyTypes as $moneyType)
        {
            array_push($moneyLabels, $moneyType->getMoneyName());
            $docs = $entityMGR->findBy('App:ACCdoc',['account'=>$account,'Money'=>$moneyType]);
            $total = 0;
            foreach ($docs as $doc){
                $total = $total + $doc->getTotalValue();
            }
            array_push($moneyTotal,$total);
        }
        $account = $entityMGR->findOneBy('App:ACCaccount',['user'=>$userMGR->currentUser()]);
        $docs = $account->getACCdocs();


        return $this->render('ceremonial/REQACCdashboard.html.twig', [
            'moneys' => $moneyTypes,
            'moneyLabels'=>$moneyLabels,
            'moneyTotals'=>$moneyTotal,
            'docs'=>$docs
        ]);
    }


    /**
     * @Route("/ceremonial/req/pasenger/delete/{id}", name="ceremonialREQpasengerDelete")
     */
    public function ceremonialREQpasengerDelete($id,Request $request,Service\LogMGR $logMGR, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if($userMGR->currentPosition()->getId() != $passenger->getSubmitter()->getId())
            return $this->redirectToRoute('403');
        $logMGR->addEvent('FRE56','حذف','مدیریت مسافران','CEREMONIAL',$request->getClientIp());
        $entityMGR->remove('App:CMPassenger',$id);
        return $this->redirectToRoute('ceremonialREQpasengers',['msg'=>3]);
    }



    //--------------------------------------------MANAGER PART ----------------------------------------


    /**
     * @Route("/ceremonial/doing/acc/balance/{msg}", name="ceremonialDOINGACCBalance")
     */
    public function ceremonialDOINGACCBalance($msg=0,Request $request,Service\ACC $ACC,Service\LogMGR $logMGR, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailMNGDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');

        $moneyLabels=[];
        $moneyTypes = $entityMGR->findAll('App:ACCMoney');
        $moneyTotal = [];
        $ic = $entityMGR->findOneBy('App:ACCiccenter',['iccode'=>1002]);
        $totalDocs = $entityMGR->findBy('App:ACCdoc',['iccenter'=>$ic]);
        foreach ($moneyTypes as $moneyType)
        {
            array_push($moneyLabels, $moneyType->getMoneyName());
            $docs = $entityMGR->findBy('App:ACCdoc',['iccenter'=>$ic,'Money'=>$moneyType]);
            $total = 0;
            foreach ($docs as $doc){
                $total = $total + $doc->getTotalValue();
            }
            array_push($moneyTotal,$total);
        }

        return $this->render('ceremonial/DOINGACCTicket.html.twig', [
            'moneys' => $moneyTypes,
            'moneyLabels'=>$moneyLabels,
            'moneyTotals'=>$moneyTotal,
            'docs'=>$totalDocs
        ]);
    }

}
