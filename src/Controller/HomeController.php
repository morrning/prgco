<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use App\Service as Service;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", options={"expose" = true})
     */
    public function home(Request $request, Service\EntityMGR $entityMGR, Service\UserMGR $userMGR, LoggerInterface $logger,Service\LogMGR $logMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('userLogin');
        $area = null;
        if($userMGR->isLogedIn())
            $area = $entityMGR->find('App:SysArea',$userMGR->currentPosition()->getDefaultArea());




        $alert = null;


        $projects = $entityMGR->getORM()->createQueryBuilder('p')
            ->select(['p.id','a.areaName','p.lastUpdate','p.Pprogress','p.Cprogress'])
            ->from('App:Project','p')
            ->innerJoin('App:SysArea','a','WITH','p.areaID = a.id')
            ->orderBy('p.Pprogress','DESC')
            ->getQuery()
            ->getResult();
        $projectNames=[];
        $pprogress=[];
        $cprogress=[];

        foreach ($projects as $project)
        {
            if(!(($project['Pprogress'] == $project['Cprogress']) && $project['Cprogress'] == 100 ) ){
                array_push($projectNames, $project['areaName']);
                array_push($pprogress,$project['Pprogress']);
                array_push($cprogress,$project['Cprogress'] );
            }
        }
        $countDowns = $entityMGR->getORM()->createQueryBuilder('p')
            ->select('p')
            ->from('App:Countdown','p')
            ->where('p.timearrive > :time')
            ->setParameter('time', time())
            ->getQuery()
            ->getResult();
        return $this->render('home/index.html.twig', [
            'area'=>$area,
            'projects'=>$projects,
            'breadcrumb'=>true,
            'pn'=>$projectNames,
            'pp'=>$pprogress,
            'cp'=>$cprogress,
            'countDowns' => $countDowns
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
