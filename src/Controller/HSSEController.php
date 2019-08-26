<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HSSEController extends AbstractController
{

    //------------------------------------------ HSSE TOTAL -----------------------------------------
    /**
     * @Route("/hsse/dashboard", name="HSSEDashboard")
     */
    public function HSSEDashboard()
    {
        return $this->render('hsse/HSEDashboard.html.twig', [
            'controller_name' => 'HSSEController',
        ]);
    }
    /**
     * @Route("/hsse/ceremonial/visa/view/{id}", name="HSSECeremonialView")
     */
    public function HSSECeremonialView($id)
    {
        return $this->render('hsse/index.html.twig', [
            'controller_name' => 'HSSEController',
        ]);
    }
}
