<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MostusedfilesController extends AbstractController
{
    /**
     * @Route("/mostusedfiles", name="mostusedfilesView")
     */
    public function index()
    {
        return $this->render('mostusedfiles/adminArchiveFiles.html.twig', [
            'controller_name' => 'MostusedfilesController',
        ]);
    }
}
