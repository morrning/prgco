<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

use App\Service;
use App\Entity;
class NewsController extends AbstractController
{
    /**
     * @Route("/news/dashboard", name="newsDashboard")
     */
    public function newsDashboard()
    {
        return $this->render('news/dashboard.html.twig', [
            'controller_name' => 'NewsController',
        ]);
    }

    /**
     * @Route("/news/new/post", name="newsNewPost")
     */
    public function newsNewPost(Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('newsPublish'))
            return $this->redirectToRoute('403');
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('title', TextType::class,['label'=>'تیتر خبر'])
            ->add('body', CKEditorType::class,['label'=>'متن خبر'])
            ->add('submit', SubmitType::class,['label'=>'درج خبر'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $post = new Entity\NewsPost();
            $post->setTitle($data['title']);
            $post->setBody($data['body']);
            $post->setDateSubmit(time());
            $post->setSubmiter($userMGR->currentPosition()->getId());
            $entityMGR->insertEntity($post);

            $logger->notice('position with username ' . $userMGR->currentUser()->getUsername() . ' submit new post.' );
            return $this->redirectToRoute('newsPosts',['msg'=>'1']);
        }

        return $this->render('news/newPost.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/news/edit/post/{id}", name="newsEditPost")
     */
    public function newsEditPost($id, Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('newsPublish'))
            return $this->redirectToRoute('403');
        $post = $entityMGR->find('App:NewsPost',$id);
        if(is_null($post))
            return $this->redirectToRoute('404');

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class,['label'=>'تیتر خبر'])
            ->add('body', CKEditorType::class,['label'=>'متن خبر'])
            ->add('submit', SubmitType::class,['label'=>'ذخیره تغییرات'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $post->setTitle($data->getTitle());
            $post->setBody($data->getBody());
            $post->setSubmiter($userMGR->currentPosition()->getId());
            $entityMGR->update($post);

            $logger->notice('position with username ' . $userMGR->currentUser()->getUsername() . ' update post ID:' . $id );
            return $this->redirectToRoute('newsPosts',['msg'=>'1']);
        }

        return $this->render('news/editPost.html.twig', [
            'form' => $form->createView(),
            'postTitle'=>$post->getTitle()
        ]);
    }

    /**
     * @Route("/news/posts/{page}", name="newsPosts")
     */
    public function newsPosts($page = 1,Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('newsPublish'))
            return $this->redirectToRoute('403');

        return $this->render('news/posts.html.twig', [
            'posts' => $entityMGR->findByPage('App:NewsPost',$page,20)
        ]);
    }
}
