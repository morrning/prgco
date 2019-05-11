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

class SuggestionController extends AbstractController
{
    /**
     * @Route("/suggestion", name="suggestion")
     */
    public function index()
    {
        return $this->render('suggestion/guestDashboard.html.twig', [
            'controller_name' => 'SuggestionController',
        ]);
    }

    /**
     * @Route("/suggestion/submit/new", name="suggestionSubmitNew")
     */
    public function suggestionSubmitNew(Request $request,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        $passenger = new Entity\Suggestion();
        $form = $this->createFormBuilder($passenger)
            ->add('fullname', TextType::class,['label'=>'نام و نام‌خانوادگی'])
            ->add('email', TextType::class,['label'=>' پست الکترونیکی'])
            ->add('tel', TextType::class,['label'=>'تلفن تماس'])
            ->add('comment',TextareaType::class,['label'=>'متن:'])
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        $jdate = new Service\Jdate();
        if ($form->isSubmitted() && $form->isValid()) {
            $passenger->setSubmitter($userMGR->currentPosition());
            $passenger->setPbirthday($jdate->jallaliToUnixTime($passenger->getPbirthday()));
            $entityMGR->insertEntity($passenger);
            return $this->redirectToRoute('ceremonialREQpasengers',['msg'=>1]);
        }

        return $this->render('suggestion/submitNew.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
