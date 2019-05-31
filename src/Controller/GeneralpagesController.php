<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GeneralpagesController extends AbstractController
{
    /**
     * @Route("/404", name="404")
     */
    public function page404()
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    }

    /**
     * @Route("/403", name="403")
     */
    public function page403()
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    }
}
