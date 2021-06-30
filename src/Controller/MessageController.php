<?php

namespace App\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

class MessageController extends AbstractController
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
     * @Route("/message/inbox", name="messageIbox")
     */
    public function messageIbox(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR,Service\ConfigMGR $configMGR)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('403');
        $logMGR->addEvent('message','مشاهده','صندوق دریافت پیام','message',$request->getClientIp());
        $alerts = [];

        return $this->render('message/inbox.html.twig', [
            'messages'=>$entityMGR->findBy('App:SysMessage',['reciver'=>$userMgr->currentPosition()],['id'=>'DESC']),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/message/outbox/{msg}", name="messageOutbox")
     */
    public function messageOutbox($msg = 0, Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR,Service\ConfigMGR $configMGR)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('403');
        $logMGR->addEvent('message','مشاهده','صندوق ارسال پیام','message',$request->getClientIp());
        $alerts = [];
        if($msg == 1)
            array_push($alerts,['type'=>'success','message'=>'پیام با موفقیت ارسال شد.']);

        return $this->render('message/oubox.html.twig', [
            'messages'=>$entityMGR->findBy('App:SysMessage',['sender'=>$userMgr->currentPosition()],['id'=>'DESC']),
            'alerts'=>$alerts
        ]);
    }

    /**
     * @Route("/message/new/{msg}", name="messageNew")
     */
    public function messageNew($msg = 0,Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR,LoggerInterface $logger)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('403');

        $message = new Entity\SysMessage();
        $form = $this->createFormBuilder($message)
            ->add('reciver', Type\AutocompleteentityType::class,[
                'class'=>Entity\SysPosition::class,
                'choice_label'=>'publicLabel',
                'choice_value' => 'id',
                'label'=>'گیرنده',
                'attr'=>[
                    'pattern'=>'positions'
                ]
            ])
            ->add('mtitle', TextType::class,['label'=>'عنوان'])
            ->add('mdes', TextareaType::class,['label'=>'متن پیام'])
            ->add('attachedFile', Type\FileboxType::class,['data_class' => null,'required'=>false,'label'=>'فایل ضمیمه:'])
            ->add('submit', SubmitType::class,['label'=>'ارسال پیام'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = [];
        if($msg == 1){
            array_push($alerts,['type'=>'danger','message'=>'پشوند فایل ارسال شده قابل قبول نیست.']);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $isValid = true;
            $file = $form->get('attachedFile')->getData();
            if(!is_null($file)){
                if($file->getClientOriginalExtension() == 'zip'  || $file->getClientOriginalExtension() == 'rar'){
                    if($file->getSize() < 2097152){
                        $guid = $this->RandomString(32);
                        $tempFileName = $guid . '.' . $file->getClientOriginalExtension();
                        $file->move(str_replace('src','public_html',dirname(__DIR__)) . '/files',$tempFileName );
                        $message->setAttachedFile($tempFileName);
                    }
                    else{
                        $isValid = false;
                        array_push($alerts,['type'=>'danger','message'=>'فایل ارسال شده بسیار حجیم است.حداکثر حجم ارسال فایل 4 مگابایت می باشد.']);
                    }
                }
                else{
                    $isValid = false;
                    array_push($alerts,['type'=>'danger','message'=>'پسوند فایل ارسال شده غیرمجاز می‌باشد.فایل‌های مجاز zip و rar هستند.']);
                }
            }
            if($isValid)
            {
                $message->setSender($userMgr->currentPosition());
                $message->setDateSend(time());
                $entityMGR->insertEntity($message);

                //send notification
                $des = sprintf('پیام جدید از %s دریافت شد.',$userMgr->currentPosition()->getPublicLabel());
                $url = $this->generateUrl('messageChat');
                $userMgr->addNotificationForUser($message->getReciver(),$des,$url);
                $logMGR->addEvent('message','ارسال','ارسال پیام داخلی','message',$request->getClientIp());
                $logger->info(sprintf('user %s send message with id %s', $userMgr->currentUser()->getUsername() , $message->getId()));
                return $this->redirectToRoute('messageOutbox',['msg'=>1]);
            }
            else{
                return $this->redirectToRoute('messageNew',['msg'=>1]);
            }
        }

        return $this->render('message/new.html.twig', [
            'form' => $form->createView(),
            'alerts' => $alerts
        ]);
    }

    /**
     * @Route("/message/view/{id}", name="messageView")
     */
    public function messageView($id, Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR,LoggerInterface $logger)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('403');
        $message = $entityMGR->find('App:SysMessage',$id);
        if(is_null($message))
            return $this->redirectToRoute('404');
        if($message->getSender() == $userMgr->currentPosition() or $message->getReciver() == $userMgr->currentPosition() ){
            if(is_null($message->getDateView()) and $message->getReciver() == $userMgr->currentPosition()){
                $message->setDateView(time());
                $entityMGR->update($message);
            }

            return $this->render('message/view.html.twig',
                [
                    'message' => $message
                ]);
        }
        return $this->redirectToRoute('403');
    }

    /**
     * @Route("/message/chat", name="messageChat")
     */
    public function messageChat( Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR,LoggerInterface $logger)
    {
        if (!$userMgr->isLogedIn())
            return $this->redirectToRoute('403');
        $messages = $entityMGR->getORM()->createQueryBuilder('m')
            ->select('m')
            ->from('App:SysMessage','m')
            ->where('m.sender = :user AND m.sender != m.reciver')
            ->orWhere('m.reciver = :user AND m.sender != m.reciver')
            ->setParameters(['user'=>$userMgr->currentPosition()])
            ->orderBy('m.dateSend','DESC')
            ->getQuery()
            ->getResult();
        $peoples = [];
        foreach ($messages as $message){
            if((!key_exists($message->getSender()->getId(),$peoples)) && ($message->getSender()->getId() != $userMgr->currentPosition()->getId())){
                $temp = ['0'=>$message->getMdes(),'1'=>$message->getSender()];
                $peoples[$message->getSender()->getId()] = $temp;
            }
            elseif((!key_exists($message->getReciver()->getId(),$peoples)) && ($message->getReciver()->getId() != $userMgr->currentPosition()->getId())){
                $temp = ['0'=>$message->getMdes(),'1'=>$message->getReciver()];
                $peoples[$message->getReciver()->getId()] = $temp;
            }
        }
        return $this->render('message/chat.html.twig',[
            'peoples' => $peoples,
            'cuser'=>$userMgr->currentPosition(),
            'positions'=>$entityMGR->findAll('App:SysPosition')
        ]);

    }

    /**
     * @Route("/message/getchat/history/{id}", name="messageGetChatHistory", options={"expose" = true})
     */
    public function messageGetChatHistory($id,Request $request, Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger,Service\LogMGR $logMGR)
    {
        if (!$userMgr->isLogedIn())
            return $this->redirectToRoute('403');
        $user = $entityMGR->find('App:SysPosition',$id);

        $messages = $entityMGR->getORM()->createQueryBuilder('m')
            ->select('m')
            ->from('App:SysMessage','m')
            ->where('m.sender = :user AND m.reciver = :au')
            ->orWhere('m.sender = :au AND m.reciver = :user')
            ->setParameters(['au'=>$userMgr->currentPosition(),'user'=>$user])
            ->orderBy('m.dateSend','ASC')
            ->getQuery()
            ->getResult();

        return $this->render('message/chatHistory.html.twig',[
            'messages'=>$messages,
            'cuser'=>$userMgr->currentPosition(),
            'duser'=>$user
        ]);
    }
    /**
     * @Route("/message/sendchat/{id}/{msg}", name="messageSendChat", options={"expose" = true})
     */
    public function messageSendChat($id,$msg,Request $request, Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger,Service\LogMGR $logMGR)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('403');
        $reciver = $entityMGR->find('App:SysPosition',$id);
        if(is_null($reciver))
            return $this->redirectToRoute('404');

        $message = new Entity\SysMessage();
        $message->setDateSend(time());
        $message->setMdes($msg);
        $message->setMtitle('بدون عنوان');
        $message->setReciver($reciver);
        $message->setSender($userMgr->currentPosition());

        $entityMGR->insertEntity($message);

        //send notification
        $des = sprintf('پیام جدید از %s دریافت شد.',$userMgr->currentPosition()->getPublicLabel());
        $url = $this->generateUrl('messageChat');
        $userMgr->addNotificationForUser($message->getReciver(),$des,$url);
        $logMGR->addEvent('message','ارسال','ارسال پیام داخلی','message',$request->getClientIp());
        $logger->info(sprintf('user %s send message with id %s', $userMgr->currentUser()->getUsername() , $message->getId()));

        return $this->render('message/messageCalback.html.twig',[
            'message' => $message,
        ]);

    }

    /**
     * @Route("/message/getfirstContact/{id}", name="messagegetfirstchatContent", options={"expose" = true})
     */
    public function messagegetfirstchatContent($id,Request $request, Service\UserMGR $userMgr,Service\EntityMGR $entityMGR, LoggerInterface $logger,Service\LogMGR $logMGR)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('403');
        $message = $entityMGR->find('App:SysMessage',$id);
        if(is_null($message))
            return $this->redirectToRoute('404');

        return $this->render('message/firstContact.html.twig',[
            'message' => $message,
        ]);

    }
}
