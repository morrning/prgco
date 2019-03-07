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
}
