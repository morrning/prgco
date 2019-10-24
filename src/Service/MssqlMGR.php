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
        try {
            $hostname = $this->servername;
            $port = 1433;
            $dbname = "padideh";
            $username = "sa";
            $pw = "";
            $dbh = new \PDO ("dblib:host=$hostname:$port;dbname=$dbname","$username","$pw");
        } catch (\PDOException $e) {
            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            exit;
        }
        $stmt = $dbh->prepare("select name from master..sysdatabases where name = db_name()");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            print_r($row);
        }
        unset($dbh); unset($stmt);

        $connectionParams = array(
            'driver'=>'sqlsrv',
            'user'=>$this->user,
            'password'=>$this->pwd,
            'host'=>$this->servername,

            'dbname'=>$this->database,
            'schema'=>'m'
        );
        $this->connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        //$str = 'Select * from DataFile WHERE Emp_No=1280078';
        //$stmt = $conn->query($str);
        //echo count($stmt->fetchAll());
    }

    public function getConnection(){
        if($this->isConfigured)
            return $this->connection;
        return null;
    }


    
}