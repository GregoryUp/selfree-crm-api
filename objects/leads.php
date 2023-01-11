<?php

class Leads {
    private $conn;
    private $table_name = "leads";

    public function __construct($db)
    {
        $this->conn = $db;
        $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    }

    public function create($name, $phone, $email, $source, $comment = '') {
        $date = date('Y-m-d H:s:i');

        $query = $this->conn->prepare("INSERT INTO `{$this->table_name}` (name, phone, email, comment, source, date) VALUES(?, ?, ?, ?, ?, ?)");

        $query->execute([$name, $phone, $email, $comment, $source, $date]);
    }
}