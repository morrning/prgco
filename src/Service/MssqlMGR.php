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
            'driver'=>'sqlsrv',
            'user'=>$this->user,
            'password'=>$this->pwd,
            'host'=>$this->servername,
            'dbname'=>$this->database,
        );
        $this->connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    }

    public function getConnection(){
        if($this->isConfigured)
            return $this->connection;
        return null;
    }


    
}