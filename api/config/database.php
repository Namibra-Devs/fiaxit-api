<?php
//$mysqli = new mysqli("localhost", "root", "", "live", "3308");

class Database {
    private $host = "localhost";
    private $database_name = "fiaxit";
    private $username = "root";
    private $password = "";
    public $conn;
    

    public function getConnection(){
        $this->conn = null;
        try{
            $this->conn = new PDO(
                "mysql:host=" .$this->host . ";
                dbname=" .$this->database_name,
                $this->username,
                $this->password
            );
        }catch(PDOException $exception){
            echo "Database could not be connected: " .$exception->getmessage();
        }
        return $this->conn;
    }
    
}

$connectDB = new Database();
$db=$connectDB->getConnection();