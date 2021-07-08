<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PMSController extends AbstractController
{
    /**
     * @Route("/pms/home", name="pmsHome")
     */
    public function pmsHome(): Response
    {
        return $this->render('pms/index.html.twig', [

        ]);
    }
}
