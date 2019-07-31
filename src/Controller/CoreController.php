<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use App\Service;
use Symfony\Component\Yaml\Yaml;


class CoreController extends AbstractController
{
    /**
     * @Route("/System/load/msg/{id}", name="SystemLoadMessage", options={"expose" = true})
     */
    public function SystemLoadMessage($id,Service\UserMGR $userMgr, Service\EntityMGR $entityMgr)
    {
        $msg = $entityMgr->findOneBy('App:SysHelp',['hid'=>$id]);
        if(!is_null($entityMgr)){
            return $this->render('modal/helpMSG.html.twig',[
                'title'=>'راهنما',
                'body'=>$msg->getDes()
            ]);
        }
    }

    /**
     * @Route("/System/jdate/validation/{jdate}", name="SystemJdateValidation", options={"expose" = true}, requirements={"jdate"=".+"})
     */
    public function SystemJdateValidation($jdate,Service\Jdate $jdateMgr)
    {
        $jdate = str_replace('**','',$jdate);
        $jdate = str_replace('**','',$jdate);
        $dateArray = explode('/',$jdate);
        if(count($dateArray) == 3)
            if($jdateMgr->jcheckdate($dateArray[1],$dateArray[2],$dateArray[0] ))
                if($dateArray[0]>1300 && $dateArray[0]<1450)
                    return new Response('ok');

        return $this->render('modal/helpMSG.html.twig',[
            'title'=>'راهنما',
            'body'=>'فرمت تاریخ وارد شده نامعتبر است. مثال 1396/02/27'
        ]);
    }
    /**
     * @Route("/System/autocomplete/load/{name}/{filter}", name="SystemAutoCompleteLoad", options={"expose" = true}, requirements={"filter"=".+"})
     */
    public function SystemAutoCompleteLoad($name,$filter,Service\EntityMGR $entityMgr)
    {
        $YmlFormat = Yaml::parse(file_get_contents(realpath(__DIR__.'/../') . '/AutocompletePattern/' . $name . '.yml'));
        $em = $entityMgr->getORM();
        $result = $em->getRepository($YmlFormat['entity'])->createQueryBuilder('r')
            ->where('r.' . $YmlFormat['label'] . ' LIKE :filter')
            ->setParameter('filter', '%' . $filter . '%')
            ->getQuery()
            ->getResult();
        $resArray = [];
        foreach ($result as $res)
        {
            $methodValue = 'Get' . ucfirst($YmlFormat['value']);
            $methodLabel = 'Get' . ucfirst($YmlFormat['label']);
            $item = ['label' => $res->$methodLabel(), 'id' => $res->$methodValue()];
            array_push($resArray,$item);
        }
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $xmlContent = $serializer->serialize($resArray, 'json');
        return new Response($xmlContent);
    }
}
