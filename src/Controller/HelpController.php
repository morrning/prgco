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

class HelpController extends AbstractController
{
    /**
     * @Route("/help/homepage/{id}", name="help", options = { "expose" = true })
     */
    public function help($id=1,Service\EntityMGR $entityMGR)
    {
        $content = $entityMGR->find('App:SysHelpContent',$id);
        if(is_null($content))
            return $this->redirectToRoute('404');

        return $this->render('help/index.html.twig', [
            'content' => $content,
            'childs'=>$entityMGR->findBy('App:SysHelpContent',['parrent'=>$content])
        ]);
    }

    /**
     * @Route("/help/content/get/childs/{PID}", name="helpGetChildsTree", options = { "expose" = true })
     */
    public function helpGetChildsTree($PID ,Service\EntityMGR $entityMGR,Service\UserMGR $userMGR,LoggerInterface $logger)
    {

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $positions = $entityMGR->findAll('App:SysHelpContent');
        $positionsArray = [];
        foreach ($positions as $position) {
            $item = [
                'id'=>$position->getId(),
                'text'=>$position->getTitle()
            ];
            if(!is_null($position->getParrent()))
                $item['parent']=$position->getParrent()->getId();
            else
                $item['parent']='#';
            array_push($positionsArray,$item);
        }
        $jsonContent = $serializer->serialize($positionsArray, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        return $response;
    }
}
