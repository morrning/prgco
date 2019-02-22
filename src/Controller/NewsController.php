<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
}
