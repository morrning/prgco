<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Form\Type as Type;
use App\Service;
use App\Entity;

class SupportController extends AbstractController
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
     * @Route("/support/{msg}", name="support")
     */
    public function support($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('userLogin');
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'درخواست شما با موفقیت ثبت شد.']);

        return $this->render('support/home.html.twig', [
            'tickets' => $entityMGR->findBy('App:SuuportTicket',['mainTicket'=>true],['dateSubmit'=>'DESC']),
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/support/ticket/new", name="supportNewTicket")
     */
    public function supportNewTicket(Request $request, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('userLogin');

        $ticket = new Entity\SuuportTicket();
        $form = $this->createFormBuilder($ticket)
            ->add('subject', TextType::class,['label'=>'عنوان:'])
            ->add('body',TextareaType::class,['label'=>'موضوع:'])

            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();

        $form->handleRequest($request);
        $guid = $this->RandomString(32);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setDateSubmit(time());
            $ticket->setSubmitter($userMGR->currentUser());
            $ticket->setMainTicket(true);
            $ticket->setUID($guid);
            $entityMGR->insertEntity($ticket);
            return $this->redirectToRoute('support',['msg'=>1]);
        }
        return $this->render('support/ticketNew.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/support/ticket/view/{id}", name="supportViewicket")
     */
    public function supportViewicket($id, Request $request, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('userLogin');
        $ticket = $entityMGR->findBy('App:SuuportTicket',['UID'=>$id]);

        if(count($ticket) == 0)
            return $this->redirectToRoute('404');
        $ticketForm = new Entity\SuuportTicket();
        $isSubmitted = false;
        $form = $this->createFormBuilder($ticketForm)
            ->add('body',TextareaType::class,['label'=>'موضوع:'])
            ->add('submit', SubmitType::class,['label'=>'ثبت پاسخ'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticketForm->setDateSubmit(time());
            $ticketForm->setSubject('پاسخ به : ' . $ticket[0]->getSubject());
            $ticketForm->setSubmitter($userMGR->currentUser());
            $ticketForm->setMainTicket(false);
            $ticketForm->setUID($ticket[0]->getUID());
            $entityMGR->insertEntity($ticketForm);
            $isSubmitted = true;
        }
        $ticket = $entityMGR->findBy('App:SuuportTicket',['UID'=>$id]);
        return $this->render('support/ticketView.html.twig', [
            'form' => $form->createView(),
            'tickets' => $ticket,
            'msg' => $isSubmitted
        ]);
    }
}
