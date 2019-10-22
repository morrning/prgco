<?php
namespace App\Service;


class MssqlMGR
{

    protected $em;
    protected $session;
    protected $user;

    function __construct(EntityMGR $entityMgr)
    {
        $config = new \Doctrine\DBAL\Configuration();
        //..
        $connectionParams = array(
            'url' => 'mssql://taksa:123456@193.168.121.7/kara',
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    }

    
}