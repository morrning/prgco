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
    public function home(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        $area = null;
        if($userMGR->isLogedIn())
            $area = $entityMGR->find('App:SysArea',$userMGR->currentPosition()->getDefaultArea());

        $projects = $entityMGR->getORM()->createQueryBuilder('p')
            ->select(['p.id','a.areaName','p.lastUpdate','p.Pprogress','p.Cprogress'])
            ->from('App:Project','p')
            ->innerJoin('App:SysArea','a','WITH','p.areaID = a.id')
            ->orderBy('p.Pprogress','DESC')
            ->getQuery()
            ->getResult();

        return $this->render('home/index.html.twig', [
            'area'=>$area,
            'projects'=>$projects,
            'breadcrumb'=>true
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
