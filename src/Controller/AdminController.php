<?php

namespace App\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Yaml\Yaml;

//json encoder classes
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Service;
use App\Entity as Entity;
use App\Form\Type as Type;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="adminDashboard")
     */
    public function index(Service\UserMGR $userMgr,Service\EntityMGR $entityMGR)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        return $this->render('admin/dashboard.html.twig', [
            'usersCount' => $entityMGR->rowsCount('App:SysUser'),
            'positionsCount' => $entityMGR->rowsCount('App:SysPosition'),
            'areaCount' => $entityMGR->rowsCount('App:SysArea'),
            'SystemVersion'=>Yaml::parseFile('../config/sarkesh.yaml')['version']
        ]);
    }

    /**
     * @Route("/admin/settings", name="adminSettings")
     */
    public function adminSettings(Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $config = $configMGR->getConfig();
        $form = $this->createFormBuilder($config)
            ->add('siteName', TextType::class,['label'=>'نام شرکت'])
            ->add('activeationCode', TextType::class,['label'=>'کد فعالسازی'])
            ->add('footerText', TextType::class,['label'=>'پانویس پورتال'])
            ->add('submit', SubmitType::class,['label'=>'ذخیره تغییرات'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->update($form->getData());
            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' change system settings.');
            $alerts = [['message'=>'تنظیمات با موفقیت ذخیره شد.','type'=>'success']];
        }

        return $this->render('admin/settings.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts
        ]);
    }

    /**
     * @Route("/admin/logs", name="adminLogs")
     */
    public function adminLogs(Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $alerts = null;
        $fileSystem = new Filesystem();
        $defaultData = ['message' => 'Type your message here'];

        $form = $this->createFormBuilder($defaultData)
            ->add('submit', SubmitType::class,['label'=>'حذف تاریخچه'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fileSystem->remove(str_replace('src','var/log/prod.log',dirname(__DIR__)));

            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' clear system log file.');
            $alerts = [['message'=>'تاریخچه با موفقیت پاک شد.','type'=>'success']];
        }
        $logs=[];
        if($fileSystem->exists(str_replace('src','var/log/prod.log',dirname(__DIR__))))
            $logs = file(str_replace('src','var/log/prod.log',dirname(__DIR__)));
        return $this->render('admin/logs.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
            'logs' => $logs
        ]);
    }

    /**
     * @Route("/admin/aries", name="adminAries")
     */
    public function adminAries(Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $area = new Entity\SysArea();
        $form = $this->createFormBuilder($area)
            ->add('areaName', TextType::class,['label'=>'نام ناحیه'])
            ->add('des', TextareaType::class,['label'=>'توضیحات','required' => false])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();
        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->insertEntity($form->getData());
            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' clear system log file.');
            $alerts = [['message'=>'ناحیه کاری با موفقیت افزوده شد.','type'=>'success']];
        }

        return $this->render('admin/aries.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
            'aries' => $entityMGR->findAll('App:SysArea')
        ]);
    }

    /**
     * @Route("/admin/groups", name="adminGroups")
     */
    public function adminGroups(Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $alerts = null;

        $groups = $entityMGR->findAll('App:SysGroup');
        foreach ($groups as $group)
            $group->setPID($entityMGR->find('App:SysArea',$group->getPID())->getAreaName());
        return $this->render('admin/groups.html.twig', [
            'alerts' => $alerts,
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/admin/positionsofgroup/{id}/{msg}", name="adminPositionsofgroup")
     */
    public function adminPositionsofgroup($id,$msg=0,Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'کاربر از گروه دسترسی حذف شد.']);

        $data = ['message'=>'message'];
        $form = $this->createFormBuilder($data)
            ->add('PositionID', Type\AutocompleteType::class,['label'=>'نام کاربر','attr'=>['pattern'=>'positions']])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(is_null($form->get('PositionID')->getData()))
            {
                $alerts = [['message'=>'سمت شغلی یافت نشد.','type'=>'danger']];
            }
            else{
                $position = $entityMGR->find('App:SysPosition',$form->get('PositionID')->getData());
                $alerts = [['message'=>'سمت شغلی با موفقیت افزوده شد.','type'=>'success']];
                $userMgr->addToGroup($position->getId(),$id);
                $logger->info(sprintf('user %s add position ID %s to group ID %s',$userMgr->currentUser()->getUsername(),$position->getId(),$id));
            }
        }

        return $this->render('admin/positionsOfGroup.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
            'positions' => $userMgr->positionsOfGroup($id),
            'group'=> $entityMGR->find('App:SysGroup',$id)
        ]);
    }

    /**
     * @Route("/admin/group/position/delete/{GID}/{UID}", name="adminDeletePositionOfGroup", options = { "expose" = true })
     */
    public function adminDeletePositionOfGroup($GID,$UID ,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $userMGR->removeFromGroup($UID,$GID);
        $logger->info(sprintf('position with username %s delete position with ID %s from Group ID %s',
            $userMGR->currentUser()->getUsername(),
            $UID,
            $GID
        ));
        return $this->redirectToRoute('adminPositionsofgroup',['id'=>$GID,'msg'=>1]);
    }

    /**
     * @Route("/admin/positions/{msg}", name="adminPositions")
     */
    public function adminPositions($msg=0, Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'پست سازمانی مورد نظر با موفقیت ایجاد شد.']);
        elseif($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'پست سازمانی مورد نظر با موفقیت ویرایش شد.']);
        $data = ['message'=>'message'];
        $form = $this->createFormBuilder($data)
            ->add('PositionID', Type\AutocompleteType::class,['label'=>'نام کاربر','attr'=>['pattern'=>'positions']])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();


        return $this->render('admin/positions.html.twig', [
            'form' => $form->createView(),
            'nodes'=>$entityMGR->findBy('App:SysPosition',['upperID'=>0]),
            'alerts' => $alerts,
        ]);
    }
    /**
     * @Route("/admin/positions/get/childs/{PID}", name="adminPositionTree", options = { "expose" = true })
     */
    public function adminPositionTree($PID ,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $positions = $entityMGR->findAll('App:SysPosition');
        $positionsArray = [];
        foreach ($positions as $position) {
            $item = [
                'id'=>$position->getId(),
                'parent'=>$position->getUpperID(),
                'text'=>$position->getPublicLabel()
            ];

            array_push($positionsArray,$item);
        }
        $jsonContent = $serializer->serialize($positionsArray, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        return $response;
    }

    /**
     * @Route("/admin/users/{page}/{msg}", name="adminUsers")
     */
    public function adminUsers($page=1,$msg=0, Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $alerts = [];
        if($msg==1)
            $alerts = [['message'=>'کاربر با موفقیت اضافه شد.','type'=>'success']];
        if($msg==2)
            $alerts = [['message'=>'کاربر با موفقیت ویرایش شد.','type'=>'success']];

        $users = $entityMGR->findByPage('App:SysUser',$page,30);


        return $this->render('admin/users.html.twig', [
            'users'=>$users,
            'alerts' => $alerts,
            'page'=>$page
        ]);
    }

    /**
     * @Route("/admin/adminEditPosition/{PID}", name="adminEditPosition" , options = { "expose" = true })
     */
    public function adminEditPosition($PID, Request $request,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $alerts = [];
        $position = $entityMGR->find('App:SysPosition',$PID);
        $oldUID = $position->getUserID();

        $form = $this->createFormBuilder($position)
            ->add('label', TextType::class,['label'=>'عنوان پست سازمانی'])
            ->add('defaultArea', EntityType::class, [
                'class'=>Entity\SysArea::class,
                'choice_label'=>'areaName',
                'choice_value' => 'id',
                'label'=>'ناحیه کاری',
                'data'=>$entityMGR->find('App:SysArea',$position->getDefaultArea()->getId()),
            ])
            ->add('userID', Type\AutoentityType::class,['class'=>'App:SysUser','choice_label'=>'fullName','label'=>'نام کاربر','attr'=>['pattern'=>'users']])
            ->add('upperID', Type\AutocompleteType::class,['label'=>'پست سازمانی بالادستی','attr'=>['pattern'=>'positions']])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(! is_null($form->get('userID')->getData()) && ! is_null($form->get('upperID')->getData()))
            {
                $user = $entityMGR->find('App:SysUser',$position->getUserID());
                $allPos = $entityMGR->findBy('App:SysPosition',['userID'=>$user->getId()]);
                //disable all user positions
                foreach ($allPos as $allPo){
                    $allPo->setIsDefault(null);
                    $entityMGR->update($allPo);
                }
                $position = $form->getData();
                $position->setDefaultArea($position->getDefaultArea());
                $position->setPublicLabel($user->getFullname() . ' - ' . $position->getLabel());
                $position->setIsDefault('1');

                if($oldUID->getId() != $user->getId())
                {
                    $oldUserPos = $entityMGR->findBy('App:SysPosition',['userID'=>$oldUID->getId()]);
                    foreach ($oldUserPos as $key => $oldUserPo)
                    {
                        if ($key === array_key_first($oldUserPos))
                            $oldUserPo->setIsDefault(1);
                        else
                            $oldUserPo->setIsDefault(null);
                        $entityMGR->update($oldUserPo);
                    }
                }
                $entityMGR->insertEntity($position);
                $logger->info(sprintf('user %s edit position with id %s', $userMgr->currentUser()->getUsername() , $position->getId()));
                return $this->redirectToRoute('adminPositions',['msg'=>2]);
            }
            array_push($alerts,['type'=>'danger','message'=>'مسئول سمت یا پست سازمانی بالادستی انتخاب نشده است.']);

        }

        return $this->render('admin/positionEdit.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
            'parent'=>$entityMGR->find('App:SysPosition',$PID)
        ]);
    }

    /**
     * @Route("/admin/system/update", name="adminSystemUpdate" , options = { "expose" = true })
     */
    public function adminSystemUpdate(Request $request,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if (!$userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://raw.githubusercontent.com/morrning/prgco/master/config/sarkesh.yaml');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $urlData = curl_exec($ch);
        curl_close($ch);

        try {
            $urlConfig = Yaml::parse($urlData);
        } catch (ParseException $exception) {
            printf('Unable to parse the YAML string: %s', $exception->getMessage());
        }

        $localConfig= Yaml::parseFile('../config/sarkesh.yaml');

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('submit', SubmitType::class,['label'=>'بزن بریم آپدیت ...'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = [];
        $out = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $out['git'] = shell_exec('git pull origin master');
            $out['cache'] = shell_exec('php ../bin/console cache:clear');
            $out['db'] = shell_exec('php ../bin/console doctrine:schema:update --force');
        }

        return $this->render('admin/systemUpdate.html.twig',[
            'output'=>$out,
            'form'=>$form->createView(),
            'alert'=>$alerts,
            'currentVer'=>$urlConfig['version'],
            'localVer'=>$localConfig['version']
        ]);


    }

    /**
     * @Route("/admin/adminNewPosition/{PID}", name="adminNewPosition" , options = { "expose" = true })
     */
    public function adminNewPosition($PID, Request $request,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $position = new Entity\SysPosition();
        $form = $this->createFormBuilder($position)
            ->add('label', TextType::class,['label'=>'عنوان پست سازمانی'])
            ->add('userID', Type\AutoentityType::class,['class'=>Entity\SysUser::class,'choice_label'=>'fullName','label'=>'نام کاربر','attr'=>['pattern'=>'users']])
            ->add('defaultArea', EntityType::class, [
                'class'=>Entity\SysArea::class,'choice_label'=>'areaName',
                'label'=>'ناحیه کاری'
            ])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = [];
        if ($form->isSubmitted() && $form->isValid()) {
            if(! is_null($form->get('userID')->getData()))
            {
                $user = $entityMGR->find('App:SysUser',$position->getUserID());
                $position->setUpperID($PID);
                $position->setPublicLabel($user->getFullname() . ' - ' . $position->getLabel());
                $position->setDefaultArea($position->getDefaultArea());
                $entityMGR->insertEntity($position);
                $logger->info(sprintf('user %s add new position with id %s', $userMgr->currentUser()->getUsername() , $position->getId()));
                return $this->redirectToRoute('adminPositions',['msg'=>1]);
            }
            array_push($alerts,['type'=>'danger','message'=>'مسئول سمت انتخاب نشده است.']);
        }

        return $this->render('admin/positionNew.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
            'parent'=>$entityMGR->find('App:SysPosition',$PID)
        ]);
    }

    /**
     * @Route("/admin/user/add", name="adminNewUser")
     */
    public function adminNewUser(Request $request,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $alerts = [];
        $user = new Entity\SysUser();
        $form = $this->createFormBuilder($user)
            ->add('fullName', TextType::class,['label'=>'نام و نام‌خانوادگی'])
            ->add('username', TextType::class,['label'=>'نام کاربری'])
            ->add('password', PasswordType::class,['label'=>'کلمه عبور'])
            ->add('mobileNum', TextType::class,['label'=>'تلفن همراه'])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword(md5($user->getPassword()));
            $entityMGR->insertEntity($user);
            $logger->info(sprintf('user %s add new user with id %s', $userMgr->currentUser()->getUsername() , $user->getId()));
            return $this->redirectToRoute('adminUsers',['msg'=>1]);

        }

        return $this->render('admin/userNew.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/admin/usersedit/{id}", name="adminUserEditUser")
     */
    public function adminUserEditUser($id,Request $request,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $alerts = [];
        $user = $entityMGR->find('App:SysUser',$id);
        $form = $this->createFormBuilder($user)
            ->add('fullName', TextType::class,['label'=>'نام و نام‌خانوادگی'])
            ->add('username', TextType::class,['label'=>'نام کاربری'])
            ->add('mobileNum', TextType::class,['label'=>'تلفن همراه'])
            ->add('submit', SubmitType::class,['label'=>'ذخیره تغییرات'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityMGR->update($user);

            //update position labels
            $positions = $entityMGR->findBy('App:SysPosition',['userID'=>$user->getId()]);
            foreach ($positions as $position)
            {
                $position->setPublicLabel($user->getFullname() . ' - ' . $position->getLabel());
                $entityMGR->update($position);
            }

            $logger->info(sprintf('user %s edit user with id %s', $userMgr->currentUser()->getUsername() , $user->getId()));
            return $this->redirectToRoute('adminUsers',['msg'=>2]);

        }

        return $this->render('admin/userEdit.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
            'user'=>$user
        ]);
    }

    /**
     * @Route("/admin/user/change/password/{id}", name="adminUserChangepassword")
     */
    public function adminUserChangepassword($id,Request $request,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $alerts = [];
        $user = $entityMGR->find('App:SysUser',$id);
        if(is_null($user))
            return $this->redirectToRoute('404');

        $form = $this->createFormBuilder($user)
            ->add('password', PasswordType::class,['label'=>'کلمه عبور'])
            ->add('submit', SubmitType::class,['label'=>'تغییر کلمه عبور'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword(md5($user->getPassword()));
            $entityMGR->update($user);
            $logger->info(sprintf('user %s change password of user with id %s', $userMgr->currentUser()->getUsername() , $user->getId()));
            return $this->redirectToRoute('adminUsers',['msg'=>2]);

        }

        return $this->render('admin/userChangePassword.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
        ]);
    }

}
