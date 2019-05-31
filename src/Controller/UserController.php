<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use App\Service;

class UserController extends AbstractController
{
    /**
     * @Route("/user/login", name="userLogin")
     */
    public function login(Request $request, Service\EntityMGR $entityMGR, Service\UserMGR $userMGR, LoggerInterface $logger)
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('username', TextType::class,['label'=>'نام کاربری'])
            ->add('password', PasswordType::class,['label'=>'کلمه عبور'])
            ->add('submit', SubmitType::class,['label'=>'ورود به پورتال'])
            ->getForm();

        $form->handleRequest($request);
        $alert = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if($userMGR->login($data['username'],$data['password']))
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
                    return $this->redirectToRoute('home');
                    $logger->info('user ' . $data['username'] .' loged in.');
                }

            }
            else{
                $logger->alert('login failor for user ' . $data['username'] );
                $alert = [['message'=>'نام‌کاربری یا کلمه‌عبور اشتباه است.','type'=>'danger']];
            }
        }

        return $this->render('user/login.html.twig', [
            'form' => $form->createView(),
            'alert'=>$alert
        ]);
    }

    /**
     * @Route("/user/logout", name="userLogout")
     */
    public function logout(Request $request, Service\UserMGR $userMGR, LoggerInterface $logger)
    {
        $logger->info('user ' . $userMGR->currentUser()->getUsername() . ' logout.');
        $userMGR->logout();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("user/change/password" ,name="userChangePassword")
     */
    public function userChangePassword(Request $request,Service\UserMGR $userMgr, Service\EntityMGR $entityMgr,LoggerInterface $logger)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('userLogin');

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('currentPassword', PasswordType::class,['label'=>'کلمه‌عبور فعلی'])
            ->add('newPassword', PasswordType::class,['label'=>'کلمه‌عبور جدید'])
            ->add('renewPassword', PasswordType::class,['label'=>'تکرار کلمه‌عبور جدید'])
            ->add('submit', SubmitType::class,['label'=>'تغییر کلمه‌عبور'])
            ->getForm();

        $form->handleRequest($request);
        $alert = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userMgr->currentUser();
            if(md5($form->get('currentPassword')->getData()) !=$user->getPassword())
                array_push($alert,['type'=>'danger','message'=>'کلمه عبور فعلی اشتباه است.']);
            if($form->get('newPassword')->getData() != $form->get('renewPassword')->getData())
                array_push($alert,['type'=>'danger','message'=>'کلمه عبور وارد شده مطابق نیستند.']);
            if(strlen($form->get('newPassword')->getData()) < 6 )
                array_push($alert,['type'=>'danger','message'=>'طول کلمه عبور باید بیشتر از شش کاراکتر باشد.']);
            if(count($alert) == 0)
            {
                $user->setPassword(md5($form->get('newPassword')->getData()));
                $entityMgr->update($user);
                $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' change own password.');
                array_push($alert,['type'=>'success','message'=>'کلمه عبور با موفقیت تغییر کرد.']);
            }
        }

        return $this->render('user/changePassword.html.twig',['form'=>$form->createView(),'alert'=>$alert]);
    }

    /**
     * @Route("user/positions" ,name="userPositions")
     */
    public function userPositions( Service\UserMGR $userMgr, Service\EntityMGR $entityMGR)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('userLogin');

        return $this->render('user/positions.html.twig',
            [
                'positions' => $entityMGR->findBy('App:SysPosition',['userID'=>$userMgr->currentUser()->getId()])
            ]
        );
    }

    /**
     * @Route("user/select/position/{id}" ,name="userChangePosition")
     */
    public function userChangePosition($id = null, Service\UserMGR $userMgr, Service\EntityMGR $entityMGR, LoggerInterface $logger)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('userLogin');

        $userID = $userMgr->currentUser()->getId();
        $position = $entityMGR->findOneBy('App:SysPosition',['id'=>$id, 'userID'=>$userID]);

        if(!is_null($position))
        {
            $entityMGR
                ->getORM()
                ->createQueryBuilder()
                ->update('App:SysPosition','p')
                ->set('p.isDefault',0)
                ->where('p.userID= :userID')
                ->setParameter('userID',$userID)
                ->getQuery()
                ->execute();

            $position = $entityMGR->find('App:SysPosition',$id);
            $position->setIsDefault(1);
            $entityMGR->update($position);

            $logger->info('user ' . $userMgr->currentUser()->getUsername() . ' change active position');
        }
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("user/profile" ,name="userViewProfile")
     */
    public function userViewProfile(Service\UserMGR $userMgr)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('userLogin');

        return $this->render('user/viewProfile.html.twig',
            [
                'user'=>$userMgr->currentUser(),
                'positions'=>$userMgr->currentUserPositions()
            ]
        );
    }

    /**
     * @Route("user/notifications" ,name="userNotifications")
     */
    public function userNotifications( Service\UserMGR $userMgr, Service\EntityMGR $entityMGR)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('userLogin');

        return $this->render('user/notifications.html.twig',
            [
                'notifis' => $userMgr->currentPosition()->getSysNotifications()
            ]
        );
    }

    /**
     * @Route("user/clear/notifications" ,name="userClearNotifications")
     */
    public function userClearNotifications(Request $request, Service\UserMGR $userMgr, Service\EntityMGR $entityMGR)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('userLogin');

        $nots = $entityMGR->findBy('App:SysNotification',['userID'=>$userMgr->currentPosition(),'viewed'=>null]);
        foreach ($nots as $not)
        {
            $not->setViewed('1');
            $not->setDateSubmit(time());
            $entityMGR->update($not);
        }
        return $this->redirect($request->server->get('HTTP_REFERER'));

    }

    /**
     * @Route("user/notification/jump/{id}" ,name="userJumpNotification")
     */
    public function userJumpNotification($id,Request $request, Service\UserMGR $userMgr, Service\EntityMGR $entityMGR)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('userLogin');

        $noti = $entityMGR->find('App:SysNotification',$id);
        if(is_null($noti))
            return $this->redirectToRoute('404');
        if($noti->getUserID()->getId() != $userMgr->currentPosition()->getId())
            return $this->redirectToRoute('403');

        $noti->setViewed(1);
        $noti->setDateSubmit(time());
        $entityMGR->update($noti);

        return $this->redirect($noti->getLinkTarget());

    }
}
