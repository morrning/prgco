<?php
namespace App\Service;


class MssqlMGR
{

    protected $servername;
    protected $database;
    protected $user;
    protected $pwd;

    //save config state
    protected $isConfigured;

    //Doctrine ORM Object
    protected $config;
    protected $connection;
    protected $orm;

    function __construct(EntityMGR $entityMgr)
    {
        $this->isConfigured = false;
    }

    public function configure($server,$database,$user,$pwd=null){
        $this->servername =$server;
        $this->database = $database;
        $this->user = $user;
        $this->pwd = $pwd;
        $this->isConfigured = true;
        $config = new \Doctrine\DBAL\Configuration();


        $connectionParams = array(
            'url' => "mssql://$this->user:$this->pwd@$this->servername/$this->database",
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $str = 'Select * from DataFile WHERE Emp_No=1280078';
        $stmt = $conn->query($str);
        echo count($stmt->fetchAll());
    }


    
}