<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service as Service;

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
    public function apps(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        $area = null;
        if($userMGR->isLogedIn())
            $area = $entityMGR->find('App:SysArea',$userMGR->currentPosition()->getDefaultArea());
        return $this->render('home/apps.html.twig', [
            'area'=>$area
        ]);
    }

}
