<?php
if (defined('DEBUG')) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

class Database
{
    // укажите свои учетные данные базы данных
    private $host = "127.0.0.1";
    private $db_name = "selfree_crm";
    private $username = "surenchik";
    private $password = "GkIy1_xuJxcM!tFi";

    // private $username = 'root';
    // private $password = 'root';

    public $conn;

    // получаем соединение с БД
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            header("Content-Type: application/json");
            http_response_code(500);
            exit(json_encode(["status" => "DB_CONN_FAILED"]));
        }

        return $this->conn;
    }
}
