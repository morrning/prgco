<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('home/index.html.twig', [

        ]);
    }

    /**
     * @Route("/apps", name="apps")
     */
    public function apps()
    {
        return $this->render('home/apps.html.twig', [

        ]);
    }

}
