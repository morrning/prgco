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
     * @Route("/phonebookarchive/{msg}/{search}", name="phonebook", requirements={"filter"=".+"})
     */
    public function index(Request $request,$msg=0,$search = null, Service\EntityMGR $entityMGR)
    {
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'مخاطب ذخیره شد']);
        elseif($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'مخاطب حذف شد.']);
        if(is_null($search))
        {
            $nums = $entityMGR->findAll('App:Phonebook');
        }

        else
        {
            $em = $entityMGR->getORM();
            $nums = $em->getRepository('App:Phonebook')->createQueryBuilder('r')
                ->where('r.username LIKE :filter')
                ->setParameter('filter', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        }
        $data = ['message'=>'message'];
        $form = $this->createFormBuilder($data)
            ->add('search', Type\AutocompleteType::class,['label'=>'نام کاربر','attr'=>['pattern'=>'phonebook']])
            ->add('submit', SubmitType::class,['label'=>'جست و جو'])
            ->getForm();
        $form->handleRequest($request);
        $alerts = [];
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('phonebook',['msg'=>0, 'search'=>$form->get('search')->getData()]);
        }
        return $this->render('phonebook/index.html.twig', [
            'nums' => $nums,
            'alert' => $alerts,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/newphonebook", name="phonebookAddNew")
     */
    public function phonebookAddNew(Request $request,Service\EntityMGR $entityMGR,LoggerInterface $logger,Service\UserMGR $userMGR)
    {

        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('403');

        $phonebook = new Entity\Phonebook();
        $form = $this->createFormBuilder($phonebook)
            ->add('username', TextType::class,['label'=>'نام'])
            ->add('tel1', TextType::class,['label'=>'تلفن 1:','required' => false])
            ->add('tel2', TextType::class,['label'=>'تلفن 1:','required' => false])
            ->add('mobile1', TextType::class,['label'=>'موبایل 1:','required' => false])
            ->add('mobile2', TextType::class,['label'=>'موبایل 2:','required' => false])
            ->add('email', TextType::class,['label'=>'پست الکترونیکی:','required' => false])
            ->add('des', TextareaType::class,['label'=>'توضیحات','required' => false])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();
        $form->handleRequest($request);
        $alerts = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $submittedData = $form->getData();
            $submittedData->setSubmitter($userMGR->currentPosition()->getId());
            $submittedData->setDateSubmit(time());
            $entityMGR->insertEntity($submittedData);
            $logger->info('user ' . $userMGR->currentUser()->getUsername() . ' add new phonebook with id:' . $phonebook->getId());
            return $this->redirectToRoute('phonebook',['msg'=>1,'search'=>0]);
        }

        return $this->render('phonebook/new.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/phonebookview/{id}", name="phonebookView")
     */
    public function phonebookView($id, Service\EntityMGR $entityMGR)
    {
        $user = $entityMGR->find('App:Phonebook',$id);
        if(is_null($user))
            return $this->redirectToRoute('404');

        return $this->render('phonebook/view.html.twig', [
            'user' => $user,
        ]);
    }
}
