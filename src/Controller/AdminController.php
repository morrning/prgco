<?php

namespace App\Controller;
use SimpleXLSX;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
//json encoder classes
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Service;
use App\Entity as Entity;
use App\Form\Type as Type;
use Twig\Extension\SandboxExtension;

class AdminController extends AbstractController
{
    /**
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 32) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    public function folderSize ($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }
        return $size;
    }

    /**
     * @Route("/le", name="LE")
     */
    public function LE(Request $request, Service\UserMGR $userMGR, Service\EntityMGR $entityMGR,Service\ConfigMGR $configMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $config = $configMGR->getConfig();
        $form = $this->createFormBuilder($config)
            ->add('activeationCode', TextType::class,['label'=>'کد فعالسازی'])
            ->add('submit', SubmitType::class,['label'=>'ذخیره'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->update($form->getData());
            $logger->info('user ' . $userMGR->currentUser()->getUsername() . ' change system lisense.');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://auth.sarkesh.org/auth.php?id=' . $configMGR->getConfig()->getActiveationCode());
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $urlData = curl_exec($ch);
            curl_close($ch);

            if ($urlData < 0)
                return $this->redirectToRoute('LE');

            return $this->redirectToRoute('adminDashboard');
        }
        return $this->render('admin/le.html.twig',['form'=>$form->createView()]);
    }

    /**
     * @Route("/admin", name="adminDashboard")
     */
    public function adminDashboard(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR,Service\ConfigMGR $configMGR)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://auth.sarkesh.org/auth.php?id=' . $configMGR->getConfig()->getActiveationCode());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $urlData = curl_exec($ch);
        curl_close($ch);

        if ($urlData < 0)
            return $this->redirectToRoute('LE');
        */
        $logMGR->addEvent('4ert','مشاهده','داشبورد مدیریت سامانه','ADMINISTRATOR',$request->getClientIp());
        return $this->render('admin/dashboard.html.twig', [
            'usersCount' => $entityMGR->rowsCount('App:SysUser'),
            'positionsCount' => $entityMGR->rowsCount('App:SysPosition'),
            'areaCount' => $entityMGR->rowsCount('App:SysArea'),
            'SystemVersion'=>Yaml::parseFile('../config/sarkesh.yaml')['version'],
            'currentTime'=>time()
        ]);
    }

    /**
     * @Route("/admin/menu/main/{msg}", name="adminMainMenu")
     */
    public function adminMainMenu($msg = 0,Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR,Service\ConfigMGR $configMGR)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('4ert','مشاهده','گزینه های منو','ADMINISTRATOR',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'پیوند مورد نظر حذف شد.']);
        elseif($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'پیوند مورد نظر اضافه شد.']);
        elseif($msg == 3)
            array_push($alerts,['type'=>'success','message'=>'پیوند مورد نظر ویرایش شد.']);

        return $this->render('admin/menus.html.twig', [
            'items'=>$entityMGR->findAll('App:SysMenuItem'),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/admin/menu/item/delete/{id}", name="adminMenuItemDelete")
     */
    public function adminMenuItemDelete($id,Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR,Service\ConfigMGR $configMGR)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('4ert','حذف','گزینه های منو','ADMINISTRATOR',$request->getClientIp());

        $item = $entityMGR->find('App:SysMenuItem',$id);
        if(is_null($item))
            return $this->redirectToRoute('404');
        $entityMGR->remove('App:SysMenuItem',$id);
        return $this->redirectToRoute('adminMainMenu',['msg'=>1]);

    }

    /**
     * @Route("/admin/menu/item/add", name="adminMenuItemAdd")
     */
    public function adminMenuItemAdd(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $item = new Entity\SysMenuItem();
        $menu = $entityMGR->findOneBy('App:SysMenu',['menuName'=>'MAIN']);
        $item->setMenu($menu);
        $form = $this->createFormBuilder($item)
            ->add('label', TextType::class,['label'=>'عنوان'])
            ->add('url', TextType::class,['label'=>'پیوند','help'=>'پیوندها به http  یا https شروع می شوند.'])
            ->add('fontawsome', TextType::class,['label'=>'فونت آیکون','help'=>'برای مشاهده لیست فونت آیکون ها به سایت fontawsome.com مراجعه کنید.'])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $item->setUpper(10);
            $item->setInternalUrl(false);
            $entityMGR->insertEntity($form->getData());
            $logMGR->addEvent('4ert','جدید','گزینه های منو','ADMINISTRATOR',$request->getClientIp());
            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' add new menuItem.');
            return $this->redirectToRoute('adminMainMenu',['msg'=>2]);

        }

        return $this->render('admin/menuItemAdd.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts
        ]);
    }

    /**
     * @Route("/admin/menu/item/edit/{id}", name="adminMenuItemEdit")
     */
    public function adminMenuItemEdit($id,Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $item = $entityMGR->find('App:SysMenuItem',$id);
        if(is_null($item))
            return $this->redirectToRoute('404');

        $menu = $entityMGR->findOneBy('App:SysMenu',['menuName'=>'MAIN']);
        $item->setMenu($menu);
        $form = $this->createFormBuilder($item)
            ->add('label', TextType::class,['label'=>'عنوان'])
            ->add('url', TextType::class,['label'=>'پیوند','help'=>'پیوندها به http  یا https شروع می شوند.'])
            ->add('fontawsome', TextType::class,['label'=>'فونت آیکون','help'=>'برای مشاهده لیست فونت آیکون ها به سایت fontawsome.com مراجعه کنید.'])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $item->setUpper(10);
            $item->setInternalUrl(false);
            $entityMGR->insertEntity($form->getData());
            $logMGR->addEvent('4ert','جدید','گزینه های منو','ADMINISTRATOR',$request->getClientIp());
            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' add new menuItem.');
            return $this->redirectToRoute('adminMainMenu',['msg'=>2]);

        }

        return $this->render('admin/menuItemAdd.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts
        ]);
    }
    /**
     * @Route("/admin/settings", name="adminSettings")
     */
    public function adminSettings(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $config = $configMGR->getConfig();
        $oldConfig = clone $config;
        $form = $this->createFormBuilder($config)
            ->add('siteName', TextType::class,['label'=>'نام شرکت'])
            ->add('activeationCode', TextType::class,['label'=>'کد فعالسازی'])
            ->add('footerText', TextareaType::class,['label'=>'پانویس پورتال'])
            ->add('USERS_MAX_COOKIE_TIME', ChoiceType::class, [
                'label'=>'حداکثر اعتبار کوکی‌های کاربران',
                'choices'  => [
                    'یک روز' => 1,
                    'سه روز' => 3,
                    'یک هفته' => 7,
                    'دو هفته' => 14,
                    'یک ماه' => 30,
                    'دو ماه' => 60,
                    'شش ماه' => 180,
                    'یک سال' => 365,
                ],
            ])
            ->add('SYS_FONT_NAME', ChoiceType::class, [
                'label'=>'فونت پیشفرض سامانه',
                'choices'  => [
                    'تاهما > پیشفرض ویندوز' => 'tahoma',
                    'شبنم > اختصاصی' => 'shabnam',
                ],
            ])
            ->add('SMS_API',TextType::class,['label'=>'کد api پنل پیامک در سایت پارس گرین'])
            ->add('SYS_LOGIN_BGRND', FileType::class,['data_class' => null,'required'=>false,'label'=>'تصویر پس زمینه صفحه ورود'])
            ->add('submit', SubmitType::class,['label'=>'ذخیره تغییرات'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('SYS_LOGIN_BGRND')->getData();
            if(!is_null($file)){
                if($file->getClientOriginalExtension() == 'gif'  || $file->getClientOriginalExtension() == 'jpg' || $file->getClientOriginalExtension() == 'jpeg' || $file->getClientOriginalExtension() == 'png'){
                    if($file->getSize() < 2097152){
                        $guid = $this->RandomString(32);
                        $tempFileName = $guid . '.' . $file->getClientOriginalExtension();
                        $file->move(str_replace('src','public_html',dirname(__DIR__)) . '/files',$tempFileName );
                        $config->setSYSLOGINBGRND($tempFileName);
                    }
                    else{
                        array_push($alerts,['type'=>'danger','message'=>'فایل ارسال شده بسیار حجیم است.حداکثر حجم ارسال فایل 4 مگابایت می باشد.']);
                    }
                }
                else{
                    array_push($alerts,['type'=>'danger','message'=>'پسوند فایل ارسال شده غیرمجاز می‌باشد.']);
                }
            }
            else{
                $config->setSYSLOGINBGRND($oldConfig->getSYSLOGINBGRND());
            }
            $entityMGR->update($form->getData());
            $logMGR->addEvent('4ert','ویرایش','تنظیمات کلی سامانه','ADMINISTRATOR',$request->getClientIp());
            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' change system settings.');
            array_push($alerts,['type'=>'success','message'=>'تنظیمات با موفقیت ذخیره شد.']);
        }

        return $this->render('admin/settings.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts
        ]);
    }

    /**
     * @Route("/admin/database/settings", name="adminDatabaseSettings")
     */
    public function adminDatabaseSettings(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $config = $configMGR->getConfig();
        $form = $this->createFormBuilder($config)
            ->add('HRM_SG_SERVERNAME', TextType::class,['label'=>'نام سرور','required'=>false,'attr'=>['style'=>'direction:ltr;']])
            ->add('HRM_SG_DATABASE', TextType::class,['label'=>'بانک اطلاعاتی','required'=>false,'attr'=>['style'=>'direction:ltr;']])
            ->add('HRM_SG_USERNAME', TextType::class,['label'=>'نام کاربری','required'=>false,'attr'=>['style'=>'direction:ltr;']])
            ->add('HRM_SG_PASSWORD', PasswordType::class,['label'=>'کلمه عبور','required'=>false,'attr'=>['style'=>'direction:ltr;']])
            ->add('HRM_PW_SERVERNAME', TextType::class,['label'=>'نام سرور','required'=>false,'attr'=>['style'=>'direction:ltr;']])
            ->add('HRM_PW_DATABASE', TextType::class,['label'=>'بانک اطلاعاتی','required'=>false,'attr'=>['style'=>'direction:ltr;']])
            ->add('HRM_PW_USERNAME', TextType::class,['label'=>'نام کاربری','required'=>false,'attr'=>['style'=>'direction:ltr;']])
            ->add('HRM_PW_PASSWORD', PasswordType::class,['label'=>'کلمه عبور','required'=>false,'attr'=>['style'=>'direction:ltr;']])
            ->add('submit', SubmitType::class,['label'=>'ذخیره تغییرات'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->update($form->getData());
            $logMGR->addEvent('4ert','ویرایش','تنظیمات کلی سامانه','ADMINISTRATOR',$request->getClientIp());
            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' change system settings.');
            $alerts = [['message'=>'تنظیمات با موفقیت ذخیره شد.','type'=>'success']];
        }

        return $this->render('admin/databaseSettings.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts
        ]);
    }

    /**
     * @Route("/admin/events/logs", name="adminEvents")
     */
    public function adminEvents(Request $request , Service\LogMGR $logMGR,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $logMGR->addEvent('4ert','مشاهده','تاریخچه رویدادهای کل سامانه','ADMINISTRATOR',$request->getClientIp());

        return $this->render('admin/events.html.twig', [
            'events'=>$entityMGR->findAll('App:SysLog')
        ]);
    }

    /**
     * @Route("/admin/logs", name="adminLogs")
     */
    public function adminLogs(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
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
            $logMGR->addEvent('4ert','حذف','تاریخچه رویدادهای سیستمی','ADMINISTRATOR',$request->getClientIp());
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
    public function adminAries(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
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
            $logMGR->addEvent('4ert','افزودن','نواحی کاری کل سامانه','ADMINISTRATOR',$request->getClientIp());
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
    public function adminPositionsofgroup($id,$msg=0,Service\LogMGR $logMGR,Request $request,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
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
                $logMGR->addEvent('4ert','افزودن','کاربران گروه‌های دسترسی','ADMINISTRATOR',$request->getClientIp());
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
    public function adminDeletePositionOfGroup($GID,$UID ,Request $request,Service\LogMGR $logMGR,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {
        if(! $userMGR->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $userMGR->removeFromGroup($UID,$GID);
        $logMGR->addEvent('4ert','حذف','کاربران گروه‌های دسترسی','ADMINISTRATOR',$request->getClientIp());
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
    public function adminPositions($msg=0, Request $request, Service\LogMGR $logMGR,Service\UserMGR $userMgr, Service\ConfigMGR $configMGR,Service\EntityMGR $entityMGR, LoggerInterface $logger)
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

        $logMGR->addEvent('4ert','مشاهده','سمت‌ها و جایگاه‌های کل سامانه','ADMINISTRATOR',$request->getClientIp());

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
     * @Route("/admin/users/{msg}", name="adminUsers")
     */
    public function adminUsers($msg=0, Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $alerts = [];
        if($msg==1)
            $alerts = [['message'=>'کاربر با موفقیت اضافه شد.','type'=>'success']];
        if($msg==2)
            $alerts = [['message'=>'کاربر با موفقیت ویرایش شد.','type'=>'success']];
        if($msg==5)
            $alerts = [['message'=>'این نام کاربری یا کد ملی یا کد پرسنلی قبلا ثبت شده است.','type'=>'warning']];

        $users = $entityMGR->findAll('App:SysUser');
        $logMGR->addEvent('4ert','مشاهده','کاربران کل سامانه','ADMINISTRATOR',$request->getClientIp());

        return $this->render('admin/users.html.twig', [
            'users'=>$users,
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/admin/scripts/{msg}", name="adminScripts")
     */
    public function adminScripts($msg=0, Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $alerts = [];
        if($msg==1)
            $alerts = [['message'=>'عملیات با موفقیت انجام شد.','type'=>'success']];
        elseif($msg==2)
            $alerts = [['message'=>'اجرای عملیات با شکست مواجه شد.','type'=>'success']];
        elseif($msg==3)
            $alerts = [['message'=>'اسکریپت با موفقیت افزوده شد.','type'=>'success']];
        elseif($msg==4)
            $alerts = [['message'=>'اسکریپت با موفقیت حذف شد.','type'=>'danger']];

        $scripts = $entityMGR->findAll('App:SysScript');
        $logMGR->addEvent('4ert','مشاهده','اسکریپت‌های سیستم','ADMINISTRATOR',$request->getClientIp());

        return $this->render('admin/scripts.html.twig', [
            'scripts'=>$scripts,
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/admin/doing/script/{id}", name="adminScriptDoing")
     */
    public function adminScriptDoing($id,Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $script = $entityMGR->find('App:SysScript',$id);
        if(is_null($script))
            return $this->redirectToRoute('404');
        eval($script->getScript());
        return $this->redirectToRoute('adminScripts',['msg'=>1]);
    }

    /**
     * @Route("/admin/delete/script/{id}", name="adminScriptDelete")
     */
    public function adminScriptDelete($id,Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $entityMGR->remove('App:SysScript',$id);
        return $this->redirectToRoute('adminScripts',['msg'=>2]);
    }
    /**
     * @Route("/admin/new/script", name="adminNewScript")
     */
    public function adminNewScript(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $script = new Entity\SysScript();
        $form = $this->createFormBuilder($script)
            ->add('title', TextType::class,['label'=>'عنوان'])
            ->add('script', TextareaType::class,['label'=>'script','attr'=>['rows'=>10,'style'=>'direction:ltr;text-align:left;']])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->insertEntity($script);
            $logMGR->addEvent('4ert','افزودن','اسکریپت های مدیریتی','ADMINISTRATOR',$request->getClientIp());
            $logger->info(sprintf('user %s add script with id %s', $userMgr->currentUser()->getUsername() , $script->getId()));
            return $this->redirectToRoute('adminScripts',['msg'=>3]);
        }

        return $this->render('admin/scriptNew.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/admin/adminEditPosition/{PID}", name="adminEditPosition" , options = { "expose" = true })
     */
    public function adminEditPosition($PID, Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
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
            ->add('roll', EntityType::class, [
                'class'=>Entity\SysRoll::class,
                'choice_label'=>'label',
                'label'=>'نقش کاربری'
            ])
            ->add('userID', Type\AutoentityType::class,['class'=>'App:SysUser','choice_label'=>'fullName','label'=>'نام کاربر','attr'=>['pattern'=>'users']])
            ->add('upperID', Type\AutocompleteType::class,['label'=>'پست سازمانی بالادستی','attr'=>['pattern'=>'positions']])
            ->add('constractor',CheckboxType::class,['required'=>false,'label'=>'پیمانکار'])
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
                $logMGR->addEvent('4ert','ویرایش','سمت‌ها و جایگاه‌های کل سامانه','ADMINISTRATOR',$request->getClientIp());
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
    public function adminSystemUpdate(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if (!$userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://raw.githubusercontent.com/morrning/prgco/master/config/sarkesh.yaml');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $urlData = curl_exec($ch);
        curl_close($ch);

        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, 'https://raw.githubusercontent.com/morrning/prgco/master/note.txt');
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch2, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        $note = curl_exec($ch2);
        curl_close($ch2);

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
            $out['git-reset'] = shell_exec('git reset --hard');
            $out['git'] = shell_exec('git pull origin master');
            $out['cache'] = shell_exec('php ../bin/console cache:clear');
            $out['db'] = shell_exec('php ../bin/console doctrine:schema:update --force');
            $logMGR->addEvent('4ert','به روز رسانی','به روز رسانی سامانه','ADMINISTRATOR',$request->getClientIp());

        }

        return $this->render('admin/systemUpdate.html.twig',[
            'output'=>$out,
            'form'=>$form->createView(),
            'alert'=>$alerts,
            'currentVer'=>$urlConfig['version'],
            'localVer'=>$localConfig['version'],
            'note'=>$note
        ]);
    }

    /**
     * @Route("/admin/system/database/import", name="adminDatabaseImport" , options = { "expose" = true })
     */
    public function adminDatabaseImport(Request $request,Service\LogMGR $logMGR,Service\ConfigMGR $configMGR,Service\UserMGR $userMgr, LoggerInterface $logger)
    {
        if (!$userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('submit', SubmitType::class,['label'=>'وارد کردن دادهای پیشفرض'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $configMGR->importBasicData();
            $alerts = [['message'=>'اطلاعات پیشفرض با موفقیت ذخیره شد.','type'=>'success']];
            $logMGR->addEvent('4ert','افزودن','وارد کردن داده های پیشفرض به بانک اطلاعاتی','ADMINISTRATOR',$request->getClientIp());
        }

        return $this->render('admin/databaseImport.html.twig',[
            'form'=>$form->createView(),
            'alerts'=>$alerts,
        ]);


    }
    /**
     * @Route("/admin/system/database/backup", name="adminDatabaseExport" , options = { "expose" = true })
     */
    public function adminDatabaseExport(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if (!$userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('submit', SubmitType::class,['label'=>'شروع عملیات پشتیبان‌گیری...'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = [];
        $output ='';
        if ($form->isSubmitted() && $form->isValid()) {
            $conn = $this->container->get('doctrine')->getConnection();
            $path = str_replace('src/Controller','DBbackups',__DIR__).'/'. date('d-m-Y') . '@'.date('h.i.s').'.sql';
            exec("mysqldump --user={$conn->getUsername()} --password={$conn->getPassword()} --host={$conn->getHost()} {$conn->getDatabase()} --result-file={$path} 2>&1", $output);
            header("Content-Type: application/force-download");
            $response =  new BinaryFileResponse($path);
            // To generate a file download, you need the mimetype of the file
            $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

            // Set the mimetype with the guesser or manually
            if($mimeTypeGuesser->isSupported()){
                // Guess the mimetype of the file according to the extension of the file
                $response->headers->set('Content-Type', $mimeTypeGuesser->guess($path));
            }else{
                // Set the mimetype of the file manually, in this case for a text file is text/plain
                $response->headers->set('Content-Type', 'text/plain');
            }

            // Set content disposition inline of the file
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'backup.sql'
            );
            return $response;
            $logMGR->addEvent('db543gv','افزودن','پشتیبان‌گیری از بانک اطلاعاتی سامانه','ADMINISTRATOR',$request->getClientIp());
        }

        return $this->render('admin/databaseExport.html.twig',[
            'form'=>$form->createView(),
            'alert'=>$alerts,
        ]);


    }
    /**
     * @Route("/admin/system/cache", name="adminCache" , options = { "expose" = true })
     */
    public function adminCache(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if (!$userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('submit', SubmitType::class,['label'=>'خانه تکانی حافظه نهان...'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = [];
        $out = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $out['cache'] = shell_exec('php ../bin/console cache:clear');
            $logMGR->addEvent('4ert','حذف','حافظه نهان کل سامانه','ADMINISTRATOR',$request->getClientIp());
        }
        $dir = __DIR__;
        $dir = str_replace('src\Controller','var\cache',$dir);
        $size = $this->folderSize($dir);
        if($size<1024){$size=$size." Bytes";}
        elseif(($size<1048576)&&($size>1023)){$size=round($size/1024, 1)." KB";}
        elseif(($size<1073741824)&&($size>1048575)){$size=round($size/1048576, 1)." MB";}
        else{$size=round($size/1073741824, 1)." GB";}

        return $this->render('admin/cache.html.twig',[
            'output'=>$out,
            'form'=>$form->createView(),
            'alert'=>$alerts,
            'size'=>$size
        ]);


    }

    /**
     * @Route("/admin/adminNewPosition/{PID}", name="adminNewPosition" , options = { "expose" = true })
     */
    public function adminNewPosition($PID, Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
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
            ->add('roll', EntityType::class, [
                'class'=>Entity\SysRoll::class,'choice_label'=>'label',
                'label'=>'نقش کاربری'
            ])
            ->add('constractor',CheckboxType::class,['required'=>false,'label'=>'پیمانکار'])

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
                if(is_null($entityMGR->findBy('App:SysPosition',['isDefault'=>'1' , 'userID'=>$user])));
                    $position->setIsDefault(1);
                $entityMGR->insertEntity($position);
                $logMGR->addEvent('4ert','افزودن','سمت‌ها و جایگاه‌های کل سامانه','ADMINISTRATOR',$request->getClientIp());
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
    public function adminNewUser(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
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
            ->add('SocialMobile', TextType::class,['label'=>'تلفن همراه شبکه‌های اجتماعی'])
            ->add('nationalCode', Type\NumbermaskType::class,['label'=>'کد ملی','attr'=>['class'=>'codeMeli']])
            ->add('employeNum', TextType::class,['label'=>'شماره پرسنلی'])
            ->add('submit', SubmitType::class,['label'=>'افزودن'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            if(is_null($entityMGR->findOneBy('App:SysUser',['username'=>$user->getUsername()])) && is_null($entityMGR->findOneBy('App:SysUser',['nationalCode'=>$user->getNationalCode()])) && is_null($entityMGR->findOneBy('App:SysUser',['employeNum'=>$user->getEmployeNum()])))
            {
                $user->setPassword(md5($user->getPassword()));
                $entityMGR->insertEntity($user);
                $logMGR->addEvent('4ert','افزودن','کاربران کل سامانه','ADMINISTRATOR',$request->getClientIp());
                $logger->info(sprintf('user %s add new user with id %s', $userMgr->currentUser()->getUsername() , $user->getId()));
                return $this->redirectToRoute('adminUsers',['msg'=>1]);
            }
            return $this->redirectToRoute('adminUsers',['msg'=>5]);
        }

        return $this->render('admin/userNew.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/admin/usersedit/{id}", name="adminUserEditUser")
     */
    public function adminUserEditUser($id,Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $alerts = [];
        $user = $entityMGR->find('App:SysUser',$id);
        $form = $this->createFormBuilder($user)
            ->add('fullName', TextType::class,['label'=>'نام و نام‌خانوادگی'])
            ->add('username', TextType::class,[
                'label'=>'نام کاربری',
                'attr'=>[
                    'readonly'=>'readonly',
                ]])
            ->add('mobileNum', TextType::class,['label'=>'تلفن همراه'])
            ->add('nationalCode', Type\NumbermaskType::class,['label'=>'کد ملی','attr'=>['class'=>'codeMeli']])
            ->add('employeNum', TextType::class,['label'=>'شماره پرسنلی'])
            ->add('suspend', ChoiceType::class, [
                'label'=>'مسدود کردن کاربر',
                'choices' => [
                    'خیر.کاربر فعال باشد' => false,
                    'بله! کاربر مسدود شود' => true,
                ],
                'multiple'=>false
            ])
            ->add('SocialMobile', TextType::class,['label'=>'تلفن همراه شبکه‌های اجتماعی'])
            ->add('submit', SubmitType::class,['label'=>'ذخیره تغییرات'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $checkedUser = $entityMGR->getORM()->createQueryBuilder('m')
                ->select('u')
                ->from('App:SysUser','u')
                ->where('u.nationalCode = :nationalCode')
                ->andWhere('u.id != :sid')
                ->setParameters(['sid'=>$id,'nationalCode'=>$user->getNationalCode()])
                ->getQuery()
                ->getResult();
            if(count($checkedUser) == 0){
                //clear session id if user suspended
                if($user->getSuspend() && $user->getUsername() != 'admin')
                    $user->setUniqueID('');
                if( $user->getUsername() == 'admin')
                    $user->setSuspend(null);
                $entityMGR->update($user);

                //update position labels
                $positions = $entityMGR->findBy('App:SysPosition',['userID'=>$user->getId()]);
                foreach ($positions as $position)
                {
                    $position->setPublicLabel($user->getFullname() . ' - ' . $position->getLabel());
                    $entityMGR->update($position);
                }

                $logMGR->addEvent('4ert','ویرایش','کاربران کل سامانه','ADMINISTRATOR',$request->getClientIp());
                $logger->info(sprintf('user %s edit user with id %s', $userMgr->currentUser()->getUsername() , $user->getId()));
                return $this->redirectToRoute('adminUsers',['msg'=>2]);
            }
            return $this->redirectToRoute('adminUsers',['msg'=>5]);

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
    public function adminUserChangepassword($id,Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
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
            $logMGR->addEvent('4ert','ویرایش','تغییر کلمه عبور کاربران کل سامانه','ADMINISTRATOR',$request->getClientIp());
            $logger->info(sprintf('user %s change password of user with id %s', $userMgr->currentUser()->getUsername() , $user->getId()));
            return $this->redirectToRoute('adminUsers',['msg'=>2]);

        }

        return $this->render('admin/userChangePassword.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/admin/rolls/{msg}", name="adminRollsList")
     */
    public function adminRollsList($msg=0,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'نقش کاربری مورد نظر با موفقیت ایجاد شد.']);
        if($msg == 2)
            array_push($alerts,['type'=>'success','message'=>'نقش کاربری مورد نظر با موفقیت ویرایش شد.']);
        return $this->render('admin/rollsList.html.twig', [
            'rolls' => $entityMGR->findAll('App:SysRoll'),
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/admin/roll/new/{id}", name="adminRollNew")
     */
    public function adminRollNew($id=0,Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $id == 0 ? $roll = new Entity\SysRoll() : $roll = $entityMGR->find('App:SysRoll',$id);
        if(is_null($roll))
            return $this->redirectToRoute('404');

        $form = $this->createFormBuilder($roll)
            ->add('label', TextType::class,['label'=>'نام نقش کاربری'])
            ->add('ictRequest', ChoiceType::class, [
                'label'=>'درخواست خدمات فناوری اطلاعات و ارتباطات',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false,
            ])
            ->add('CeremonailREQ', ChoiceType::class, [
                'label'=>'درخواست تشریفات سازمانی',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('CeremonailMNGDashboard', ChoiceType::class, [
                'label'=>'مدیریت تایید سامانه تشریفات',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('ictDoing', ChoiceType::class, [
                'label'=>'مجری خدمات فناوری اطلاعات و ارتباطات',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('newsPublish', ChoiceType::class, [
                'label'=>'مدیریت بخش اعلانات و انتشار خبر',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('MostUsedFiles', ChoiceType::class, [
                'label'=>'مدیریت فایل‌های پراستفاده',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('phonebookAdmin', ChoiceType::class, [
                'label'=>'مدیریت دفترتلفن',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('suggestionInbox', ChoiceType::class, [
                'label'=>'مدیریت نظام پیشنهادات و انتقادات',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('ISOfORMS', ChoiceType::class, [
                'label'=>'مدیریت سامانه IMS',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('CountDown', ChoiceType::class, [
                'label'=>'مدیریت سامانه روزشمار و تقویم',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('projectAdmin', ChoiceType::class, [
                'label'=>'مدیریت سامانه کنترل پروژه کل',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'data'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->insertEntity($roll);
            $logMGR->addEvent('adminRoll','افزودن','نقش کاربری','ADMINISTRATOR',$request->getClientIp());
            $logger->info(sprintf('user %s add new roll with id %s', $userMgr->currentUser()->getUsername() , $roll->getId()));
            $id == 0 ? $msg=1: $msg=2;
            return $this->redirectToRoute('adminRollsList',['msg'=>$msg]);

        }
        return $this->render('admin/rollNew.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/roll/edit/{id}", name="adminRollEdit")
     */
    public function adminRollEdit($id=0,Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $id == 0 ? $roll = new Entity\SysRoll() : $roll = $entityMGR->find('App:SysRoll',$id);
        if(is_null($roll))
            return $this->redirectToRoute('404');

        $form = $this->createFormBuilder($roll)
            ->add('label', TextType::class,['label'=>'نام نقش کاربری'])
            ->add('ictRequest', ChoiceType::class, [
                'label'=>'درخواست خدمات فناوری اطلاعات و ارتباطات',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false,
            ])
            ->add('ictDoing', ChoiceType::class, [
                'label'=>'مجری خدمات فناوری اطلاعات و ارتباطات',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('newsPublish', ChoiceType::class, [
                'label'=>'مدیریت بخش اعلانات و انتشار خبر',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('CeremonailREQ', ChoiceType::class, [
                'label'=>'درخواست تشریفات سازمانی',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('CeremonailMNGDashboard', ChoiceType::class, [
                'label'=>'مدیریت تایید سامانه تشریفات',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('MostUsedFiles', ChoiceType::class, [
                'label'=>'مدیریت فایل‌های پراستفاده',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('phonebookAdmin', ChoiceType::class, [
                'label'=>'مدیریت دفترتلفن',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('suggestionInbox', ChoiceType::class, [
                'label'=>'مدیریت نظام پیشنهادات و انتقادات',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('ISOfORMS', ChoiceType::class, [
                'label'=>'مدیریت سامانه IMS',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('CountDown', ChoiceType::class, [
                'label'=>'مدیریت سامانه روزشمار و تقویم',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('projectAdmin', ChoiceType::class, [
                'label'=>'مدیریت سامانه کنترل پروژه کل',
                'choices' => [
                    'غیرمجاز' => false,
                    'مجاز' => true,
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('submit', SubmitType::class,['label'=>'ثبت'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityMGR->insertEntity($roll);
            $logMGR->addEvent('adminRoll','افزودن','نقش کاربری','ADMINISTRATOR',$request->getClientIp());
            $logger->info(sprintf('user %s add new roll with id %s', $userMgr->currentUser()->getUsername() , $roll->getId()));
            $id == 0 ? $msg=1: $msg=2;
            return $this->redirectToRoute('adminRollsList',['msg'=>$msg]);

        }
        return $this->render('admin/rollEdit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/plugins/edit", name="adminPluginEdit")
     */
    public function adminPluginEdit(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');
        $ceremonial = $entityMGR->findOneBy('App:SysBundle',['bundleName'=>'CEREMONIAL']);
        $ict = $entityMGR->findOneBy('App:SysBundle',['bundleName'=>'ICT']);

        $dataForm = ['messsage'=>'default'];

        $form = $this->createFormBuilder($dataForm)
            ->add('ICT', ChoiceType::class, [
                'label'=>'زیرسیستم فناوری اطلاعات و ارتباطات',
                'choices' => [
                    'فعال' => false,
                    'غیرفعال' => true,
                ],
                'data'=>$ict->getIsDisabled(),
                'expanded'=>true,
                'multiple'=>false,
            ])
            ->add('CEREMONIAL', ChoiceType::class, [
                'label'=>'زیرسیستم تشریفات',
                'choices' => [
                    'فعال' => false,
                    'غیرفعال' => true,
                ],
                'data'=>$ceremonial->getIsDisabled(),
                'expanded'=>true,
                'multiple'=>false,
            ])
            ->add('submit', SubmitType::class,['label'=>'ثبت تغییرات'])
            ->getForm();
        $alerts = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ceremonial->setIsDisabled($form->get('CEREMONIAL')->getData());
            $entityMGR->update($ceremonial);

            $ict->setIsDisabled($form->get('ICT')->getData());
            $entityMGR->update($ict);

            $logMGR->addEvent('adminPlugins','ویرایش','مدیریت زیرسیستم‌ها','ADMINISTRATOR',$request->getClientIp());
            $logger->info(sprintf('user %s edit system plugins', $userMgr->currentUser()->getUsername() ));

            array_push($alerts,['type'=>'success','message'=>'تغییرات با موفقیت ثبت شد.']);

        }
        return $this->render('admin/pluginsManage.html.twig', [
            'form' => $form->createView(),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/admin/imp", name="adminP")
     */
    public function adminP(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->hasPermission('superAdmin'))
            return $this->redirectToRoute('403');

        $ptype = $entityMGR->findOneBy('App:CMPassengerType',['typeName'=>'پرسنل شرکت']);
        $passportType = $entityMGR->findOneBy('App:CMPassengerDocType',['tname'=>'گذرنامه']);
        $pesonalPicType = $entityMGR->findOneBy('App:CMPassengerDocType',['tname'=>'عکس پرسنلی']);
        $passengers = \SimpleXLSX::parse('D:\projects\prgco\public_html\a.xlsx')->rows();
        foreach ($passengers as $passenger){
            $user = $entityMGR->findOneBy('App:SysUser',['nationalCode'=>$passenger[5]]);
            if(!is_null($user)){
                $pos = $entityMGR->findOneBy('App:SysPosition',['userID'=>$user]);
                if(!is_null($pos)){
                    if(is_null($entityMGR->findOneBy('App:CMPassenger',['pcodemeli'=>$passenger[5]]))){
                        $pass = new Entity\CMPassenger();
                        $pass->setPtype($ptype);
                        $pass->setPname($passenger[0]);
                        $pass->setPfamily($passenger[1]);
                        $pass->setPfather($passenger[2]);
                        $pass->setPbirthday($passenger[3]);
                        $pass->setPshenasname($passenger[4]);
                        $pass->setPcodemeli($passenger[5]);
                        $pass->setPassportExpireDate($passenger[6]);
                        $pass->setTel1($passenger[7]);
                        $pass->setTel2($passenger[8]);
                        $pass->setAdr($passenger[9]);
                        $pass->setLname($passenger[10]);
                        $pass->setLfamily($passenger[11]);
                        $pass->setLfather($passenger[12]);
                        $pass->setPassNO($passenger[13]);
                        $pass->setSubmitter($pos);
                        $entityMGR->insertEntity($pass);

                        $docPass = new Entity\CMPassengerPersonalDoc();
                        $docPass->setPassenger($pass);
                        $docPass->setDocName('ceremonial/' . $pass->getPcodemeli() . '.jpg');
                        $docPass->setDoctype($passportType);
                        $entityMGR->insertEntity($docPass);

                        $docPic = new Entity\CMPassengerPersonalDoc();
                        $docPic->setPassenger($pass);
                        $docPic->setDocName('ProfilePic/' . $pass->getPcodemeli() . '.jpg');
                        $docPic->setDoctype($pesonalPicType);
                        $entityMGR->insertEntity($docPic);
                        echo 'user :' . $pass->getPcodemeli() . 'added <br>';
                    }

                }
            }

        }
        return new Response(1);
    }

    /**
     * @Route("/jobs/j1", name="jobsj1")
     */
    public function jobsj1(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        $roll = $entityMGR->find('App:SysRoll',3);
        $area = $entityMGR->find('App:SysArea',3);
        $user = $entityMGR->find('App:SysUser',1);
        $passengers = \SimpleXLSX::parse('D:\projects\prgco\public_html\123.xlsx')->rows();
        foreach ($passengers as $passenger){
            $sysPos = new Entity\SysPosition();
            $sysPos->setConstractor(0);
            $sysPos->setDefaultArea($area);
            $sysPos->setUpperID(1);
            $sysPos->setUserID($user);
            $sysPos->setRoll($roll);
            $sysPos->setLabel($passenger[0]);
            $sysPos->setPublicLabel('سرپرست سامانه - ' . $passenger[0]);
            $sysPos->setDefaultArea($area);
            $entityMGR->insertEntity($sysPos);
            echo 'position  :' . $sysPos->getPublicLabel() . 'added <br>';
            return new Response('end');
        }
    }
}
