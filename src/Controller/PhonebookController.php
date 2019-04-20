<?php

namespace App\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

//json encoder classes
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Service;
use App\Entity as Entity;
use App\Form\Type as Type;

class PhonebookController extends AbstractController
{
    /**
     * @Route("/phonebook", name="phonebook")
     */
    public function index(Service\EntityMGR $entityMGR)
    {

        return $this->render('phonebook/index.html.twig', [
            'nums' => $entityMGR->findAll('App:Phonebook'),
        ]);
    }

    /**
     * @Route("/phonebook/new", name="phonebookAddNew")
     */
    public function phonebookAddNew(Request $request,Service\EntityMGR $entityMGR,LoggerInterface $logger,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');

        $area = new Entity\Phonebook();
        $form = $this->createFormBuilder($area)
            ->add('username', TextType::class,['label'=>'نام'])
            ->add('tel1', TextType::class,['label'=>'تلفن 1:','required' => false])
            ->add('tel2', TextType::class,['label'=>'تلفن 1:','required' => false])
            ->add('mobile1', TextType::class,['label'=>'موبایل 1:','required' => false])
            ->add('mobile2', TextType::class,['label'=>'موبایل 2:','required' => false])
            ->add('des', TextareaType::class,['label'=>'توضیحات','required' => false])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();
        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->insertEntity($form->getData());
            $logger->info('user ' . $userMGR->currentUser()->getUsername() . ' clear system log file.');
            $alerts = [['message'=>'ناحیه کاری با موفقیت افزوده شد.','type'=>'success']];
        }

        return $this->render('phonebook/new.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
        ]);
    }
}
