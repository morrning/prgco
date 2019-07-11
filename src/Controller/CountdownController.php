<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CountdownController extends AbstractController
{
    /**
     * @Route("/countdown", name="countdown")
     */
    public function index()
    {
        return $this->render('countdown/index.html.twig', [
            'controller_name' => 'CountdownController',
        ]);
    }
}
