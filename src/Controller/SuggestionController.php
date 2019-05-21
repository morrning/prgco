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
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 32) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * @Route("/suggestion", name="suggestion")
     */
    public function suggestion()
    {
        return $this->render('suggestion/guestDashboard.html.twig', [
            'controller_name' => 'SuggestionController',
        ]);
    }

    /**
     * @Route("/suggestion/success/{id}", name="suggestionSubmitSuccess")
     */
    public function suggestionSubmitSuccess($id)
    {
        return $this->render('suggestion/guestSubmitSuccess.html.twig', [
            'id' => $id,
        ]);
    }

    /**
     * @Route("/suggestion/submit/new", name="suggestionSubmitNew")
     */
    public function suggestionSubmitNew(Request $request,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        $suggestion = new Entity\Suggestion();
        $form = $this->createFormBuilder($suggestion)
            ->add('Stype', ChoiceType::class,['label'=>'نوع درخواست','choices'=>['پیشنهاد و راهکار'=>'2','انتقاد'=>'1']])
            ->add('fullname', TextType::class,['label'=>'نام و نام‌خانوادگی','required'=>false])
            ->add('email', TextType::class,['label'=>' پست الکترونیکی','required'=>false,'attr'=>['placeholder'=>'youremail@example.com']])
            ->add('tel', TextType::class,['label'=>'تلفن تماس','required'=>false,'attr'=>['placeholder'=>'0912345678']])
            ->add('comment',TextareaType::class,['label'=>'متن:','attr'=>['rows'=>12]])
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $suggestion->setDateSubmit(time());
            $suggestion->setParrentID('#');
            $suggestion->setSID($this->RandomString(8));

            $entityMGR->insertEntity($suggestion);
            return $this->redirectToRoute('suggestionSubmitSuccess',['id'=>$suggestion->getSID()]);
        }

        return $this->render('suggestion/submitNew.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/suggestion/search", name="suggestionSearch")
     */
    public function suggestionSearch(Request $request,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        $suggestion = new Entity\Suggestion();
        $form = $this->createFormBuilder($suggestion)
            ->add('SID', TextType::class,['label'=>'کد پیگیری'])
            ->add('submit', SubmitType::class,['label'=>'جست و جو'])
            ->getForm();
        $alerts =[];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $res = $entityMGR->findOneBy('App:Suggestion',['SID'=>$form->get('SID')->getData()]);
            if(! is_null($res))
            {
                return $this->redirectToRoute('suggestionView',['id'=>$res->getSID()]);
            }
            $alerts = [['message'=>'درخواست مورد نظر در سامانه موجود نیست.لطفا مجددا سعی کنید.','type'=>'danger']];

        }

        return $this->render('suggestion/guestSearch.html.twig', [
            'form' => $form->createView(),
            'alert'=>$alerts
        ]);
    }

    /**
     * @Route("/suggestion/view/{id}", name="suggestionView")
     */
    public function suggestionView($id,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        $res = $entityMGR->findOneBy('App:Suggestion',['SID'=>$id]);
        if(is_null($res))
            return $this->redirectToRoute('404');

        return $this->render('suggestion/suggestionView.html.twig', [
            'req' => $res,
            'doing'=>$entityMGR->findBy('App:Suggestion',['parrentID'=>$res->getId()])
        ]);
    }
}
