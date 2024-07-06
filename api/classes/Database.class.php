<?php
date_default_timezone_set('Asia/Kolkata');
class Database
{
    
    public function __construct()
    {
        return self::getInstance();
    }
    
    public static function getInstance()
    {
         
         $dsn = 'mysql:host='.Config::DB_HOST.';dbname='.Config::DB_NAME;
         $user = Config::DB_USERNAME;
         $password = Config::DB_PASSWORD;   
         $options = [];
    
        try {
            
        $conn = new PDO($dsn, $user, $password,$options);
        $conn->exec("SET time_zone = '+5:30';");
            
        }
        catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "";
            die();
        }
        return $conn;
    }
    
}





