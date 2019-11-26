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
     * @Route("/message/new", name="messageNew")
     */
    public function messageNew(Request $request,Service\LogMGR $logMGR,Service\UserMGR $userMgr,Service\EntityMGR $entityMGR,LoggerInterface $logger)
    {
        if(! $userMgr->isLogedIn())
            return $this->redirectToRoute('403');

        $message = new Entity\SysMessage();
        $form = $this->createFormBuilder($message)
            ->add('reciver', Type\AutocompleteentityType::class,[
                'class'=>Entity\SysPosition::class,
                'choice_label'=>'publicLabel',
                'choice_value' => 'id',
                'label'=>'فرستنده',
                'attr'=>[
                    'pattern'=>'position'
                ]
            ])
            ->add('mtitle', TextType::class,['label'=>'عنوان'])
            ->add('mdes', TextareaType::class,['label'=>'متن پیام'])
            ->add('attachedFile', FileType::class,['data_class' => null,'required'=>false,'label'=>'فایل ضمیمه:'])
            ->add('submit', SubmitType::class,['label'=>'ارسال پیام'])
            ->getForm();

        $form->handleRequest($request);
        $alerts = null;
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
                $url = $this->generateUrl('messageView',['id'=>$message->getId()]);
                $userMgr->addNotificationForUser($message->getReciver(),$des,$url);
                $logMGR->addEvent('message','ارسال','ارسال پیام داخلی','message',$request->getClientIp());
                $logger->info(sprintf('user %s send message with id %s', $userMgr->currentUser()->getUsername() , $message->getId()));
                return $this->redirectToRoute('messageOutbox',['msg'=>1]);
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
}
