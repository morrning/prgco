<?php
/**
 * Created by PhpStorm.
 * User: BABAK
 * Date: 3/28/2019
 * Time: 10:25 AM
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity as Entity;

class PositionMGR
{
    protected $em;


    function __construct(EntityMGR $entityMgr)
    {
        $this->em = $entityMgr;
    }


}