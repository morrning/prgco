<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

use App\Service;
use App\Entity;

class ProjectController extends AbstractController
{
    /**
     * @Route("/project/admin/dashboard", name="projectAdminDashboard")
     */
    public function projectAdminDashboard(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->hasPermission('projectAdmin','PROJECT'))
            return $this->redirectToRoute('403');

        $projects = $entityMGR->getORM()->createQueryBuilder('p')
            ->select(['p.id','a.areaName','p.lastUpdate','p.Pprogress','p.Cprogress'])
            ->from('App:Project','p')
            ->innerJoin('App:SysArea','a','WITH','p.areaID = a.id')
            ->getQuery()
            ->getResult();

        return $this->render('project/dashboard.html.twig', [
            'newsCount'=> $entityMGR->rowsCount('App:NewsPost'),
            'projects'=>$projects
        ]);
    }

    /**
     * @Route("/project/admin/list/{msg}", name="ProjectAdminProjects")
     */
    public function ProjectAdminProjects($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {

        if(! $userMGR->hasPermission('projectAdmin','PROJECT'))
            return $this->redirectToRoute('403');

        $alerts = null;
        if($msg == 1)
            $alerts = [['type'=>'success','message'=>'پروژه با موفقت افزوده شد.']];
        elseif($msg == 2)
            $alerts = [['type'=>'success','message'=>'پروژه با موفقت ویرایش شد.']];

        $orm = $entityMGR->getORM();
        $projects = $orm->createQueryBuilder('p')
            ->select(['p.id','a.areaName','p.lastUpdate','p.Pprogress','p.Cprogress'])
            ->from('App:Project','p')
            ->innerJoin('App:SysArea','a','WITH','p.areaID = a.id')
            ->getQuery()
            ->getResult();
        return $this->render('project/projects.html.twig', [
            'projects' => $projects,
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/project/admin/new/Project", name="ProjectAdminNewProject")
     */
    public function ProjectAdminNewProject(Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('projectAdmin','PROJECT'))
            return $this->redirectToRoute('403');

        $project = new Entity\Project();
        $form = $this->createFormBuilder($project)
            ->add('areaID', EntityType::class,
                [
                    'label'=>'ناحیه کاری',
                    'class'=>Entity\SysArea::class,
                    'choice_value'=>'id',
                    'choice_label'=>'areaName'
                ]
            )
            ->add('pprogress', NumberType::class,['label'=>'پیشرفت فیزیکی'])
            ->add('cprogress', NumberType::class,['label'=>'پیشرفت برنامه ای'])
            ->add('submit', SubmitType::class,['label'=>'افزودن پروژه'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            if(is_null($entityMGR->findOneBy('App:Project',['areaID'=>$form->get('areaID')->getData()])))
            {
              $project = new Entity\Project();
              $project->setAreaID($form->get('areaID')->getData());
              $project->setLastUpdate(time());
              $project->setPprogress($form->get('pprogress')->getData());
              $project->setCprogress($form->get('cprogress')->getData());
              $entityMGR->insertEntity($project);
              return $this->redirectToRoute('ProjectAdminProjects',['msg'=>1]);
              $logger->info('position with username ' . $userMGR->currentUser()->getUsername() . ' submit new Project.' );
            }
            $alert = [['type'=>'warning','message'=>'این ناحیه کاری قبلا اضافه شده است']];
        }

        return $this->render('project/newProject.html.twig', [
            'form' => $form->createView(),
            'alerts'=>$alert
        ]);
    }

    /**
     * @Route("/project/adminEdit/Project/{id}", name="ProjectAdminEditProject")
     */
    public function ProjectAdminEditProject($id,Request $request,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if (!$userMGR->hasPermission('projectAdmin', 'PROJECT'))
            return $this->redirectToRoute('403');

        $project = $entityMGR->find('App:Project',$id);
        $form = $this->createFormBuilder($project)
            ->add('areaID', EntityType::class,
                [
                    'label' => 'ناحیه کاری',
                    'class' => Entity\SysArea::class,
                    'choice_value' => 'id',
                    'choice_label' => 'areaName'
                ]
            )
            ->add('pprogress', NumberType::class, ['label' => 'پیشرفت فیزیکی'])
            ->add('cprogress', NumberType::class, ['label' => 'پیشرفت برنامه ای'])
            ->add('submit', SubmitType::class, ['label' => 'ذخیره تغییرات'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $project->setLastUpdate(time());
            $entityMGR->update($project);
            return $this->redirectToRoute('ProjectAdminProjects', ['msg' => 2]);
            $logger->info('position with username ' . $userMGR->currentUser()->getUsername() . ' update Project data with id:' . $project->getId());
            $alerts = [['type' => 'success', 'message' => 'این ناحیه کاری قبلا اضافه شده است']];
        }

        return $this->render('project/newProject.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alert
        ]);
    }

}
