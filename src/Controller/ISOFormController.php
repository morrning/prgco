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

class ISOFormController extends AbstractController
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
     * @Route("/isoforms", name="ISOformsView")
     */
    public function ISOformsView(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        return $this->render('iso_form/mostUsedFiles.html.twig', [
            'files' => $entityMGR->findBy('App:ISOForm',['formType'=>$entityMGR->findOneBy('App:ISOFormType',['typeCode'=>1])]),
            'insts' => $entityMGR->findBy('App:ISOForm',['formType'=>$entityMGR->findOneBy('App:ISOFormType',['typeCode'=>2])]),
        ]);
    }

    /**
     * @Route("/isoforms/dashboard/{msg}", name="isoformsDashboard")
     */
    public function isoformsDashboard($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ISOfORMS','ISOFORM'))
            return $this->redirectToRoute('403');

        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'فرم با موفقیت حذف شد']);
        elseif($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'فرم با موفقیت اضافه شد.']);
        elseif($msg == 3)
            array_push($alerts,['type'=>'success','message'=>'فرم با موفقیت ویرایش شد.']);

        return $this->render('iso_form/dashboard.html.twig', [
            'forms' => $entityMGR->findBy('App:ISOForm',['formType'=>$entityMGR->findOneBy('App:ISOFormType',['typeCode'=>1])],['dateSubmit'=>'DESC']),
            'insts' => $entityMGR->findBy('App:ISOForm',['formType'=>$entityMGR->findOneBy('App:ISOFormType',['typeCode'=>2])],['dateSubmit'=>'DESC']),
            'alerts' => $alerts
        ]);
    }

    /**
     * @Route("/isoforms/admin/forms/{type}", name="isoformsForms")
     */
    public function isoformsForms($type = 1,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        //type  forms====>1 insts====>2
        if(! $userMGR->hasPermission('ISOfORMS','ISOFORM'))
            return $this->redirectToRoute('403');
        $entitys = $entityMGR->findBy('App:ISOForm',['formType'=>$entityMGR->findOneBy('App:ISOFormType',['typeCode'=>$type])]);
        $formType = 'فرم‌ها';
        if($type == 2)
            $formType = 'دستورالعمل‌ها';
        return $this->render('iso_form/adminArchiveFiles.html.twig', [
            'files' => $entitys,
            'formType'=>$formType
        ]);
    }

    /**
     * @Route("/isoforms/delete/file/{id}", name="isoformsDeleteFile")
     */
    public function isoformsDeleteFile($id = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('ISOfORMS','ISOFORM'))
            return $this->redirectToRoute('403');

        $entityMGR->remove('App:ISOForm',$id);
        return $this->redirectToRoute('isoformsDashboard',['msg'=>1]);
    }

    /**
     * @Route("/isoforms/new/file", name="isoformsNewFile")
     */
    public function isoformsNewFile(Request $request, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('ISOfORMS','ISOFORM'))
            return $this->redirectToRoute('403');
        $alerts = [];
        $data = new Entity\ISOForm();
        $form = $this->createFormBuilder($data)
            ->add('title', TextType::class,['label'=>'عنوان:'])
            ->add('cat',EntityType::class,['label'=>'دسته بندی','class'=>Entity\ISOFormCat::class,'choice_label'=>'catName','choice_value'=>'id'])
            ->add('isoCode', TextType::class,['label'=>'شناسه ایزو:'])
            ->add('fileID',FileType::class,['label'=>'فایل :'])
            ->add('formType', EntityType::class, [
                'class'=>Entity\ISOFormType::class,
                'choice_label'=>'typeName',
                'choice_value' => 'id',
                'label'=>'نوع مدرک'
            ])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();

        $form->handleRequest($request);
        $file = $form->get('fileID')->getData();
        $guid = $this->RandomString(32);
        if ($form->isSubmitted() && $form->isValid()) {
            if($file->getClientOriginalExtension() == 'pdf'  || $file->getClientOriginalExtension() == 'docx' || $file->getClientOriginalExtension() == 'xls' || $file->getClientOriginalExtension() == 'doc' || $file->getClientOriginalExtension() == 'xlsx'){
                if($file->getSize() < 1010920907152){
                    $tempFileName = $guid . '.' . $file->getClientOriginalExtension();
                    $file->move(str_replace('src','public_html',dirname(__DIR__)) . '/files',$tempFileName );
                    $data->setSubmitter($userMGR->currentPosition()->getId());
                    $data->setFileID($tempFileName);
                    $data->setDateSubmit(time());
                    $data->setFileExt($file->getClientOriginalExtension());
                    $entityMGR->insertEntity($data);
                    return $this->redirectToRoute('isoformsDashboard',['msg'=>2]);
                }
                else{
                    array_push($alerts,['type'=>'danger','message'=>'فایل ارسال شده بسیار حجیم است.حداکثر حجم ارسال فایل 8 مگابایت می باشد.']);
                }
            }
            else
                array_push($alerts, ['type'=>'danger','message'=>'نوع فایل وارد شده صحیح نیست.لطفا فایل ,xls,pdf,docx  ارسال فرمایید.']);
        }
        return $this->render('iso_form/adminNewFile.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/isoforms/edit/file/{id}", name="isoformsEditFile")
     */
    public function isoformsEditFile($id, Request $request, Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('ISOfORMS','ISOFORM'))
            return $this->redirectToRoute('403');
        $data = $entityMGR->find('App:ISOForm',$id);
        if(is_null($data))
            return $this->redirectToRoute('404');

        $lastFileID = $data->getFileID();
        $data->setFileID(null);

        $alerts = [];
        $form = $this->createFormBuilder($data)
            ->add('title', TextType::class,['label'=>'عنوان:'])
            ->add('cat',EntityType::class,['label'=>'دسته بندی','class'=>Entity\ISOFormCat::class,'choice_label'=>'catName','choice_value'=>'id'])
            ->add('isoCode', TextType::class,['label'=>'شناسه ایزو:'])
            ->add('fileID',FileType::class,['label'=>'فایل :','required'=>false])
            ->add('formType', EntityType::class, [
                'class'=>Entity\ISOFormType::class,
                'choice_label'=>'typeName',
                'choice_value' => 'id',
                'label'=>'نوع مدرک'
            ])
            ->add('submit', SubmitType::class,['label'=>'ذخیره تغییرات'])
            ->getForm();

        $form->handleRequest($request);
        $file = $form->get('fileID')->getData();
        $guid = $this->RandomString(32);
        if ($form->isSubmitted() && $form->isValid()) {
            if(is_null($data->getFileID())){
                $data->setFileID($lastFileID);
                $entityMGR->update($data);
                return $this->redirectToRoute('isoformsDashboard',['msg'=>3]);
            }
            else{
                if($file->getClientOriginalExtension() == 'pdf'  || $file->getClientOriginalExtension() == 'docx' || $file->getClientOriginalExtension() == 'xls' || $file->getClientOriginalExtension() == 'doc' || $file->getClientOriginalExtension() == 'xlsx'){
                    if($file->getSize() < 1010920907152){
                        $tempFileName = $guid . '.' . $file->getClientOriginalExtension();
                        $file->move(str_replace('src','public_html',dirname(__DIR__)) . '/files',$tempFileName );
                        $data->setSubmitter($userMGR->currentPosition()->getId());
                        $data->setFileID($tempFileName);
                        $data->setDateSubmit(time());
                        $data->setFileExt($file->getClientOriginalExtension());
                        $entityMGR->update($data);
                        return $this->redirectToRoute('isoformsDashboard',['msg'=>3]);
                    }
                    else{
                        array_push($alerts,['type'=>'danger','message'=>'فایل ارسال شده بسیار حجیم است.حداکثر حجم ارسال فایل 8 مگابایت می باشد.']);
                    }
                }
                else
                    array_push($alerts, ['type'=>'danger','message'=>'نوع فایل وارد شده صحیح نیست.لطفا فایل ,xls,pdf,docx  ارسال فرمایید.']);
            }

        }
        return $this->render('iso_form/adminEditFile.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
        ]);
    }
}

