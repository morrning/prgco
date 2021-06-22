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

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('username', TextType::class,['label'=>'نام کاربری','attr'=>['placeholder'=>'نام کاربری']])
            ->add('password', PasswordType::class,['label'=>'کلمه عبور','attr'=>['placeholder'=>'کلمه عبور']])
            ->add('submit', SubmitType::class,['label'=>'ورود به پورتال'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if($userMGR->login($data['username'],$data['password'],true))
            {
                $position = $entityMGR->findOneBy('App:SysPosition',['userID'=>$userMGR->currentUser()]);
                if(is_null($position))
                {
                    $userMGR->logout();
                    $alert = [['message'=>'هیچ پست سازمانی برای شما تعریف نشده است.','type'=>'danger']];
                }
                else{
                    if(is_null($entityMGR->findOneBy('App:SysPosition',['userID'=>$userMGR->currentUser(),'isDefault'=>'1']))){
                        $position->setIsDefault(1);
                        $entityMGR->update($position);
                    }
                    if($userMGR->currentUser()->getSuspend())
                    {
                        $userMGR->logout();
                        $alert = [['message'=>'حساب کاربری شما توسط مدیرسامانه مسدود شده است.در صورتی که فکر می‌کنید اشتباهی رخ داده لطفا با مدیر سامانه تماس بگیرید.','type'=>'danger']];
                    }
                    else{
                        $logger->info('user ' . $data['username'] .' loged in.');
                        $logMGR->addEvent('3gv5','ورود به سامانه',sprintf('کاربر با نام کاربری %s وارد سامانه شد.',$data['username']),'USERS',$request->getClientIp());
                        return $this->redirectToRoute('home');
                    }

                }

            }
            else{
                $logger->alert('login failor for user ' . $data['username'] );
                $alert = [['message'=>'نام‌کاربری یا کلمه‌عبور اشتباه است.','type'=>'danger']];
            }
        }

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

        return $this->render('home/index.html.twig', [
            'area'=>$area,
            'projects'=>$projects,
            'breadcrumb'=>true,
            'pn'=>$projectNames,
            'pp'=>$pprogress,
            'cp'=>$cprogress,
            'form'=>$form->createView(),
            'alert'=>$alert
        ]);
    }
    /**
     * @Route("/home/new", name="homeNew")
     */
    public function homeNew(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {

        return $this->render('home/new.html.twig', [

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
