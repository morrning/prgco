<?php

namespace App\Controller;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Form\Type as Type;


use App\Service;
use App\Entity;

class SuggestionController extends AbstractController
{
    /**
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 32) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * @Route("/suggestion", name="suggestion")
     */
    public function suggestion()
    {
        return $this->render('suggestion/guestDashboard.html.twig', [
            'controller_name' => 'SuggestionController',
        ]);
    }

    /**
     * @Route("/suggestion/success/{id}", name="suggestionSubmitSuccess")
     */
    public function suggestionSubmitSuccess($id)
    {
        return $this->render('suggestion/guestSubmitSuccess.html.twig', [
            'id' => $id,
        ]);
    }

    /**
     * @Route("/suggestion/submit/new", name="suggestionSubmitNew")
     */
    public function suggestionSubmitNew(Request $request,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        $suggestion = new Entity\Suggestion();
        $form = $this->createFormBuilder($suggestion)
            ->add('Stype', ChoiceType::class,['label'=>'نوع درخواست','choices'=>['پیشنهاد و راهکار'=>'2','انتقاد'=>'1']])
            ->add('fullname', TextType::class,['label'=>'نام و نام‌خانوادگی','required'=>false])
            ->add('email', TextType::class,['label'=>' پست الکترونیکی','required'=>false,'attr'=>['placeholder'=>'youremail@example.com']])
            ->add('tel', TextType::class,['label'=>'تلفن تماس','required'=>false,'attr'=>['placeholder'=>'0912345678']])
            ->add('comment',TextareaType::class,['label'=>'متن:','attr'=>['rows'=>12]])
            ->add('captcha', CaptchaType::class)
            ->add('submit', SubmitType::class,['label'=>'ثبت اطلاعات'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $suggestion->setDateSubmit(time());
            $suggestion->setParrentID('#');
            $suggestion->setSID($this->RandomString(8));
            $entityMGR->insertEntity($suggestion);
            //send notification to admins
            $des = 'یک درخواست جدید در صندوق پیشنهادات و انتقادات ثبت شد.';
            $url = $this->generateUrl('suggestionInbox');
            $userMGR->addNotificationForGroup('suggestionInbox','SUGGESTION',$des,$url);
            return $this->redirectToRoute('suggestionSubmitSuccess',['id'=>$suggestion->getSID()]);
        }

        return $this->render('suggestion/submitNew.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/suggestion/search", name="suggestionSearch")
     */
    public function suggestionSearch(Request $request,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        $suggestion = new Entity\Suggestion();
        $form = $this->createFormBuilder($suggestion)
            ->add('SID', TextType::class,['label'=>'کد پیگیری'])
            ->add('submit', SubmitType::class,['label'=>'جست و جو'])
            ->getForm();
        $alerts =[];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $res = $entityMGR->findOneBy('App:Suggestion',['SID'=>$form->get('SID')->getData()]);
            if(! is_null($res))
            {
                return $this->redirectToRoute('suggestionView',['id'=>$res->getSID()]);
            }
            $alerts = [['message'=>'درخواست مورد نظر در سامانه موجود نیست.لطفا مجددا سعی کنید.','type'=>'danger']];

        }

        return $this->render('suggestion/guestSearch.html.twig', [
            'form' => $form->createView(),
            'alert'=>$alerts
        ]);
    }

    /**
     * @Route("/suggestion/view/{id}", name="suggestionView")
     */
    public function suggestionView($id,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        $res = $entityMGR->findOneBy('App:Suggestion',['SID'=>$id]);
        if(is_null($res))
            return $this->redirectToRoute('404');

        return $this->render('suggestion/suggestionView.html.twig', [
            'req' => $res,
            'referrals'=>$entityMGR->findBy('App:SuggestionReferral',['suggestion'=>$res,'guestView'=>1])
        ]);
    }

    /**
     * @Route("/suggestion/admin", name="suggestionAdmin")
     */
    public function suggestionAdmin(Service\EntityMGR $entityMGR,Service\UserMGR $userMGR)
    {
        if(! $userMGR->isLogedIn())
            return $this->redirectToRoute('userLogin');

        //get counts
        $type1 = count($entityMGR->findBy('App:Suggestion',['Stype'=>1]));
        $type2 = count($entityMGR->findBy('App:Suggestion',['Stype'=>2]));
        $refAll = count($entityMGR->findBy('App:SuggestionReferral',['user'=>$userMGR->currentPosition()]));
        $refUnread = count($entityMGR->findBy('App:SuggestionReferral',['user'=>$userMGR->currentPosition(),'dateView'=>null]));
        return $this->render('suggestion/adminDashboard.html.twig', [
            'type1Count' => $type1,
            'type2Count' => $type2,
            'refAll'=>$refAll,
            'refUnread'=>$refUnread
        ]);
    }

    /**
     * @Route("/suggestion/admin/inbox/{msg}", name="suggestionInbox")
     */
    public function suggestionInbox($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {

        if(! $userMGR->hasPermission('suggestionInbox','SUGGESTION'))
            return $this->redirectToRoute('403');

        $alerts = null;
        if($msg == 1)
            $alerts = [['type'=>'success','message'=>'درخواست با موفقیت ارجاع شد.']];

        return $this->render('suggestion/adminInbox.html.twig', [
            'reqs' => $entityMGR->findAll('App:Suggestion'),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/suggestion/admin/view/{id}", name="suggestionAdminView")
     */
    public function suggestionAdminView(Request $request,$id,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        $res = $entityMGR->findOneBy('App:Suggestion',['SID'=>$id]);
        if(is_null($res))
            return $this->redirectToRoute('404');

        //check referals view
        $items = $entityMGR->findBy('App:SuggestionReferral',['user'=>$userMGR->currentPosition(),'suggestion'=>$res]);
        foreach ($items as $item)
        {
            $item->setDateView(time());
            $entityMGR->update($item);
        }
        $ref = new Entity\SuggestionReferral();
        $form = $this->createFormBuilder($ref)
            ->add('guestView', CheckboxType::class, ['label' => 'برای ارباب رجوع نمایش داده شود؟', 'required' => false])
            ->add('user', Type\AutoentityType::class,['class'=>'App:SysPosition','choice_label'=>'publicLabel','label'=>'ارجاع گیرنده:','attr'=>['pattern'=>'positions']])
            ->add('des',TextareaType::class,['label'=>'متن:','attr'=>['rows'=>5]])
            ->add('submit', SubmitType::class,['label'=>'ارجاع'])
            ->getForm();
        $alerts = null;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ref->setDateSubmit(time());
            $ref->setSuggestion($res);
            $ref->setReferralSource($userMGR->currentPosition());
            $entityMGR->insertEntity($ref);

            //send notification to admins
            $des = 'یک ارجاع جدید در نظام پیشنهادات و انتقادات دریافت کردید.';
            $url = $this->generateUrl('suggestionReferralInbox');
            $userMGR->addNotificationForUser($ref->getUser(),$des,$url);
            $alerts = [['type'=>'success','message'=>'درخواست با موفقیت ارجاع شد.']];
        }

        return $this->render('suggestion/adminSuggestionView.html.twig', [
            'req' => $res,
            'referrals'=>$res->getSuggestionReferrals(),
            'form'=>$form->createView(),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/suggestion/admin/other/view/{id}", name="suggestionOtherAdminView")
     */
    public function suggestionOtherAdminView(Request $request,$id,Service\UserMGR $userMGR,Service\EntityMGR $entityMGR)
    {
        $res = $entityMGR->findOneBy('App:Suggestion',['SID'=>$id]);
        if(is_null($res))
            return $this->redirectToRoute('404');

        //check referals view
        $items = $entityMGR->findBy('App:SuggestionReferral',['user'=>$userMGR->currentPosition(),'suggestion'=>$res]);
        foreach ($items as $item)
        {
            $item->setDateView(time());
            $entityMGR->update($item);
        }

        $ref = new Entity\SuggestionReferral();
        $form = $this->createFormBuilder($ref)
            ->add('guestView', CheckboxType::class, ['label' => 'برای ارباب رجوع نمایش داده شود؟', 'required' => false])
            ->add('user', Type\AutoentityType::class,['class'=>'App:SysPosition','choice_label'=>'publicLabel','label'=>'ارجاع گیرنده:','attr'=>['pattern'=>'positions']])
            ->add('des',TextareaType::class,['label'=>'متن:','attr'=>['rows'=>5]])
            ->add('submit', SubmitType::class,['label'=>'ارجاع'])
            ->getForm();
        $alerts = null;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ref->setDateSubmit(time());
            $ref->setSuggestion($res);
            $ref->setReferralSource($userMGR->currentPosition());
            $entityMGR->insertEntity($ref);

            //send notification to admins
            $des = 'یک ارجاع جدید در نظام پیشنهادات و انتقادات دریافت کردید.';
            $url = $this->generateUrl('suggestionInbox');
            $userMGR->addNotificationForUser($ref->getUser(),$des,$url);
            $alerts = [['type'=>'success','message'=>'درخواست با موفقیت ارجاع شد.']];
        }

        return $this->render('suggestion/adminOtherSuggestionView.html.twig', [
            'req' => $res,
            'referrals'=>$res->getSuggestionReferrals(),
            'form'=>$form->createView(),
            'alerts'=>$alerts
        ]);
    }
    /**
     * @Route("/suggestion/referrals/inbox/{msg}", name="suggestionReferralInbox")
     */
    public function suggestionReferralInbox($msg = 0,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {

        $alerts = null;
        if($msg == 1)
            $alerts = [['type'=>'success','message'=>'درخواست با موفقیت ارجاع شد.']];

        return $this->render('suggestion/adminReferrals.html.twig', [
            'refs' => $entityMGR->findBy('App:SuggestionReferral',['user'=>$userMGR->currentPosition()]),
            'alerts'=>$alerts
        ]);
    }
}
