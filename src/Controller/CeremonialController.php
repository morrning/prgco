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

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Form\Type as Type;


use App\Service;
use App\Entity;

class CeremonialController extends AbstractController
{
    /**
     * @Route("/ceremonial/req/dashboard", name="ceremonialREQDashboard")
     */
    public function ceremonialREQDashboard(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        return $this->render('ceremonial/REQDashboard.html.twig', [
            'controller_name' => 'CeremonialController',
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasengers/{msg}", name="ceremonialREQpasengers")
     */
    public function ceremonialREQpasengers($msg=0, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'اطلاعات مسافر اضافه شد.']);
        elseif($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'اطلاعات مسافر ویرایش شد.']);
        elseif($msg == 3)
            array_push($alerts,['type'=>'success','message'=>'اطلاعات مسافر حذف شد.']);

        return $this->render('ceremonial/REQPassengers.html.twig', [
            'passengers' => $userMGR->currentPosition()->getcMPassengers(),
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasenger/delete/{id}", name="ceremonialREQpasengerDelete")
     */
    public function ceremonialREQpasengerDelete($id, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if($userMGR->currentPosition()->getId() != $passenger->getSubmitter()->getId())
            return $this->redirectToRoute('403');
        $entityMGR->remove('App:CMPassenger',$id);
        return $this->redirectToRoute('ceremonialREQpasengers',['msg'=>3]);
    }

    /**
     * @Route("/ceremonial/req/pasenger/view/{id}", name="ceremonialREQpasengerView")
     */
    public function ceremonialREQpasengerView($id,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');
        $passenger = $entityMGR->find('App:CMPassenger',$id);
        if(is_null($passenger))
            return $this->redirectToRoute('404');
        elseif ($passenger->getSubmitter()->getId() != $userMGR->currentPosition()->getId())
            return $this->redirectToRoute('403');

        return $this->render('ceremonial/viewPassengerInfo.html.twig', [
            'passenger' => $passenger
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasenger/new", name="ceremonialREQpasengerNew")
     */
    public function ceremonialREQpasengerNew(Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $passenger = new Entity\CMPassenger();
        $form = $this->createFormBuilder($passenger)
            ->add('pname', TextType::class,['label'=>'نام'])
            ->add('pfamily', TextType::class,['label'=>' نام خانوادگی'])
            ->add('pfather', TextType::class,['label'=>' نام پدر'])
            ->add('pbirthday',Type\JdateType::class,['label'=>'تاریخ تولد'])
            ->add('pshenasname', TextType::class,['label'=>' شماره شناسنامه'])
            ->add('pcodemeli', TextType::class,['label'=>'کد ملی'])
            ->add('visaNo', TextType::class,['label'=>'Visa Number:'])
            ->add('passNo', TextType::class,['label'=>'Passport Number:'])
            ->add('lname', TextType::class,['label'=>'Name:'])
            ->add('lfamily', TextType::class,['label'=>'Family:'])
            ->add('lfather', TextType::class,['label'=>'Father Name:', 'required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        $jdate = new Service\Jdate();
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            if (is_null($entityMGR->findOneBy('App:CMPassenger', ['pcodemeli' => $passenger->getPcodemeli()]))) {
                $passenger->setSubmitter($userMGR->currentPosition());
                $passenger->setPbirthday($jdate->jallaliToUnixTime($passenger->getPbirthday()));
                $entityMGR->insertEntity($passenger);
                return $this->redirectToRoute('ceremonialREQpasengers', ['msg' => 1]);
            }
            $alert = [['type' => 'danger', 'message' => 'این کد ملی قبلا ثبت شده است.']];
        }
        return $this->render('ceremonial/newPasenger.html.twig', [
            'alerts'=>$alert,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ceremonial/req/pasenger/edit/{id}", name="ceremonialREQpasengerEdit")
     */
    public function ceremonialREQpasengerEdit($id, Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('CeremonailREQ','CEREMONIAL',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $passenger = $entityMGR->find('App:CMPassenger',$id);
        $form = $this->createFormBuilder($passenger)
            ->add('pname', TextType::class,['label'=>'نام'])
            ->add('pfamily', TextType::class,['label'=>' نام خانوادگی'])
            ->add('pfather', TextType::class,['label'=>' نام پدر'])
            ->add('pbirthday',Type\JdateType::class,['label'=>'تاریخ تولد'])
            ->add('visaNo', TextType::class,['label'=>'Visa Number:'])
            ->add('passNo', TextType::class,['label'=>'Passport Number:'])
            ->add('lname', TextType::class,['label'=>'Name:'])
            ->add('lfamily', TextType::class,['label'=>'Family:'])
            ->add('lfather', TextType::class,['label'=>'Father Name:', 'required'=>false])
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        $jdate = new Service\Jdate();
        if ($form->isSubmitted() && $form->isValid()) {
            $passenger->setSubmitter($userMGR->currentPosition());
            $passenger->setPbirthday($jdate->jallaliToUnixTime($passenger->getPbirthday()));
            $entityMGR->update($passenger);
            return $this->redirectToRoute('ceremonialREQpasengers',['msg'=>2]);
        }
        return $this->render('ceremonial/editPassenger.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ceremonial/req/air/ticket/new", name="ceremonialREQAIRpaneNew")
     */
    public function ceremonialREQAIRpaneNew(Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {

    }
}
