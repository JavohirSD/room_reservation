<?php
class DB {
      private $connection;
      private static $instance;

      private $dbhost;
      private $dbuser;
      private $dbpass;
      private $dbname;
      private $port;
      private $charset;
     
      // returns new instance only in case existing not found
      public static function getInstance(){
        if(!self::$instance) {
              self::$instance = new self();
           }
          return self::$instance;
        }

    
      private function __construct() {
        try{
        	global $config;
        	$this->dbhost  = $config['dbhost'];
        	$this->dbuser  = $config['dbuser'];
        	$this->dbname  = $config['dbname'];
        	$this->dbpass  = $config['dbpass'];
        	$this->port    = $config['port'];
        	$this->charset = $config['charset'];


        $this->connection = new PDO('mysql:host='.$this->dbhost.';port='.$this->dbhost.';dbname='.$this->dbname.';charset='.$this->charset, $this->dbuser, $this->dbpass);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
          die("Failed to connect to DB: ". $e->getMessage());
        }
      }

      // Magic method clone is overriden to prevent duplication of connection
      private function __clone(){}
      
      // Get the connection
      public function getConnection(){
        return $this->connection;
      }
    }
  ?>