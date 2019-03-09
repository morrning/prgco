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


use App\Service;
use App\Entity as Entity;
use App\Form\Type as Type;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="adminDashboard")
     */
    public function index(Service\UserMGR $userMgr)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/settings", name="adminSettings")
     */
    public function adminSettings(Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $config = $configMGR->getConfig();
        $form = $this->createFormBuilder($config)
            ->add('siteName', TextType::class,['label'=>'نام شرکت'])
            ->add('activeationCode', TextType::class,['label'=>'کد فعالسازی'])
            ->add('footerText', TextType::class,['label'=>'پانویس پورتال'])
            ->add('submit', SubmitType::class,['label'=>'ذخیره تغییرات'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->update($form->getData());
            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' change system settings.');
            $alerts = [['message'=>'تنظیمات با موفقیت ذخیره شد.','type'=>'success']];
        }

        return $this->render('admin/settings.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts
        ]);
    }

    /**
     * @Route("/admin/logs", name="adminLogs")
     */
    public function adminLogs(Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('submit', SubmitType::class,['label'=>'حذف تاریخچه'])
            ->getForm();
        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $fileSystem = new Filesystem();
            $fileSystem->remove(str_replace('src','var/log/prod.log',dirname(__DIR__)));

            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' clear system log file.');
            $alerts = [['message'=>'تاریخچه با موفقیت پاک شد.','type'=>'success']];
        }

        $logs = file(str_replace('src','var/log/prod.log',dirname(__DIR__)));
        return $this->render('admin/logs.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
            'logs' => $logs
        ]);
    }

    /**
     * @Route("/admin/aries", name="adminAries")
     */
    public function adminAries(Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $area = new Entity\SysArea();
        $form = $this->createFormBuilder($area)
            ->add('areaName', TextType::class,['label'=>'نام ناحیه'])
            ->add('des', TextareaType::class,['label'=>'توضیحات','required' => false])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();
        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->insertEntity($form->getData());
            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' clear system log file.');
            $alerts = [['message'=>'ناحیه کاری با موفقیت افزوده شد.','type'=>'success']];
        }

        return $this->render('admin/aries.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
            'aries' => $entityMGR->findAll('App:SysArea')
        ]);
    }

    /**
     * @Route("/admin/groups", name="adminGroups")
     */
    public function adminGroups(Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $alerts = null;

        $groups = $entityMGR->findAll('App:SysGroup');

        return $this->render('admin/groups.html.twig', [
            'alerts' => $alerts,
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/admin/positionsofgroup/{id}", name="adminPositionsofgroup")
     */
    public function adminPositionsofgroup($id,Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');


        $area = new Entity\SysArea();
        $form = $this->createFormBuilder($area)
            ->add('areaName', Type\AutocompleteType::class,['label'=>'نام کاربر','attr'=>['pattern'=>'positions']])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();
        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->insertEntity($form->getData());
            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' clear system log file.');
            $alerts = [['message'=>'ناحیه کاری با موفقیت افزوده شد.','type'=>'success']];
        }

        return $this->render('admin/positionsOfGroup.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
            'positions' => $userMgr->positionsOfGroup($id),
            'group'=> $entityMGR->find('App:SysGroup',$id)
        ]);
    }

    /**
     * @Route("/admin/group/position/delete/{GID}/{UID}", name="adminDeletePositionOfGroup", options = { "expose" = true })
     */
    public function adminDeletePositionOfGroup($GID,$UID ,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $userMGR->removeFromGroup($UID,$GID);
        $logger->info(sprintf('position with username %s delete position with ID %s from Group ID %s',
            $userMGR->currentUser()->getUsername(),
            $UID,
            $GID
        ));
        return new Response(200);
    }

}
