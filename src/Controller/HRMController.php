<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service;
class HRMController extends AbstractController
{
    /**
     * @Route("/hrm/admin", name="HRMadmin")
     */
    public function HRMadmin(Service\MssqlMGR $mssqlMgr)
    {
        
        return $this->render('hrm/index.html.twig', [
            'controller_name' => 'HRMController',
        ]);
    }
}
