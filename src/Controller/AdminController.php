<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use App\Service;
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
            $logger->notice('user ' . $userMgr->currentUser()->getUsername() . ' change system settings.');
            $alerts = [['message'=>'تنظیمات با موفقیت ذخیره شد.','type'=>'success']];
        }

        return $this->render('admin/settings.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts
        ]);
    }
}
