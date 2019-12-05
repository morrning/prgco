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

class VisaController extends AbstractController
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
     * @Route("/ceremonial/opt/visa/deliver/{id}", name="ceremonialOPTVisaDeliver")
     */
    public function ceremonialOPTVisaDeliver($id,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');

        $visa = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');
        $passenger = $visa->getPassenger();
        $visaState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>3]);
        $visa->setVisaState($visaState);
        $visa->setDateReciveToCo(time());
        $visa->setReciver($userMGR->currentPosition());
        $entityMGR->update($visa);

        $logMGR->addEvent('CERVISA'.$visa->getId(),'اعلام وصول','درخواست ویزا','CEREMONIAL',$request->getClientIp());
        $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','درخواست ویزا','CEREMONIAL',$request->getClientIp());
        $des = sprintf('درخواست ویزا توسط %s اعلام وصول شد.',$userMGR->currentPosition()->getPublicLabel());
        $url = $this->generateUrl('HSSECeremonialView',['id'=>$visa->getId()]);
        $userMGR->addNotificationForGroup('HSSETOTAL','HSSE',$des,$url);
        $des = sprintf('درخواست ویزا %s اعلام وصول شد.',$visa->getSubmitter()->getPublicLabel());
        $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
        $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);

        return $this->redirectToRoute('ceremonialOPTVisaView',['id'=>$visa->getId(),'msg'=>1]);
    }

    /**
     * @Route("/ceremonial/opt/visa/consulate/{id}/{type}", name="ceremonialOPTVisaConsulate")
     */
    public function ceremonialOPTVisaConsulate($id,$type='in',Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailOPTDashboard','CEREMONIAL'))
            return $this->redirectToRoute('403');

        $visa = $entityMGR->find('App:CMVisaReq',$id);
        if(is_null($visa))
            return $this->redirectToRoute('404');
        $passenger = $visa->getPassenger();
        if($type == 'in'){
            $visaState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>5]);
            $visa->setVisaState($visaState);
            $visa->setDateInputConsulate(time());
            $visa->setConsulateImporter($userMGR->currentPosition());
            $entityMGR->update($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'تحویل گذرنامه به کنسولگری','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $des = sprintf('گذرنامه توسط %s به کنسولگری تحویل داده شد.',$visa->getSubmitter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);
            return $this->redirectToRoute('ceremonialOPTVisaView',['id'=>$visa->getId(),'msg'=>2]);
        }
        if($type == 'out'){
            $visaState = $entityMGR->findOneBy('App:CMVisaState',['StateCode'=>6]);
            $visa->setVisaState($visaState);
            $visa->setDateOutputConsulate(time());
            $visa->setConsulateExporter($userMGR->currentPosition());
            $entityMGR->update($visa);

            $logMGR->addEvent('CERVISA'.$visa->getId(),'تحویل از کنسولگری','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $logMGR->addEvent('CERPASSENGER'.$passenger->getId(),'افزودن','درخواست ویزا','CEREMONIAL',$request->getClientIp());
            $des = sprintf('گذرنامه توسط %s از کنسولگری تحویل گرفته شد.',$visa->getSubmitter()->getPublicLabel());
            $url = $this->generateUrl('ceremonialREQVisaView',['id'=>$visa->getId()]);
            $userMGR->addNotificationForUser($visa->getSubmitter(),$des,$url);
            return $this->redirectToRoute('ceremonialOPTVisaView',['id'=>$visa->getId(),'msg'=>3]);
        }
    }
}
