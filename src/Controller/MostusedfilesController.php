<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

use App\Service;
use App\Entity;

class MostusedfilesController extends AbstractController
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
     * @Route("/mostusedfiles", name="mostusedfilesView")
     */
    public function mostusedfilesView(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('userLogin');


        return $this->render('mostusedfiles/mostUsedFiles.html.twig', [
            'files' => $entityMGR->findBy('App:MostUsedFile',['areaID'=>$userMGR->currentPosition()->getDefaultArea()]),
        ]);
    }

    /**
     * @Route("/mostusedfiles/dashboard/{msg}", name="mostusedfilesDashboard")
     */
    public function mostusedfilesDashboard($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('MostUsedFiles','MUF',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'فرم با موفقیت حذف شد']);
        elseif($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'فرم با موفقیت اضافه شد.']);

        return $this->render('mostusedfiles/adminArchiveFiles.html.twig', [
            'files' => $entityMGR->findBy('App:MostUsedFile',['areaID'=>$userMGR->currentPosition()->getDefaultArea()]),
            'alerts' => $alerts
        ]);
    }

    /**
     * @Route("/mostusedfiles/delete/file/{id}", name="mostusedfilesDeleteFile")
     */
    public function mostusedfilesDeleteFile($id = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('MostUsedFiles','MUF',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $entityMGR->remove('App:MostUsedFile',$id);
        return $this->redirectToRoute('mostusedfilesDashboard',['msg'=>1]);
    }

    /**
     * @Route("/mostusedfiles/new/file", name="mostusedfilesNewFile")
     */
    public function mostusedfilesNewFile(Request $request, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('MostUsedFiles','MUF',null,$userMGR->currentPosition()->getDefaultArea()))
            return $this->redirectToRoute('403');

        $alerts = [];

        $data = ['message'=>'message'];
        $form = $this->createFormBuilder($data)
            ->add('title', TextType::class,['label'=>'عنوان:'])
            ->add('fileID',FileType::class,['label'=>'فایل:'])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();

        $form->handleRequest($request);
        $file = $form->get('fileID')->getData();
        $guid = $this->RandomString(32);
        if ($form->isSubmitted() && $form->isValid()) {
            if($file->getClientOriginalExtension() == 'pdf'  || $file->getClientOriginalExtension() == 'docx'){
                if($file->getSize() < 2097152){
                    $tempFileName = $guid . '.' . $file->getClientOriginalExtension();
                    $file->move(str_replace('src','public_html',dirname(__DIR__)) . '/files',$tempFileName );
                    $muf = new Entity\MostUsedFile();
                    $muf->setAreaID($userMGR->currentPosition()->getDefaultArea());
                    $muf->setTitle($form->get('title')->getData());
                    $muf->setSubmitter($userMGR->currentPosition()->getId());
                    $muf->setFileID($tempFileName);
                    $entityMGR->insertEntity($muf);
                    return $this->redirectToRoute('mostusedfilesDashboard',['msg'=>2]);
                }
                else{
                    array_push($alerts,['type'=>'danger','message'=>'فایل ارسال شده بسیار حجیم است.حداکثر حجم ارسال فایل 2 مگابایت می باشد.']);
                }
            }
            else{
                array_push($alerts, ['type'=>'danger','message'=>'نوع فایل وارد شده صحیح نیست.لطفا فایل pdf,docx  ارسال فرمایید.']);
            }

        }

        return $this->render('mostusedfiles/adminNewFile.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
        ]);
    }
}
