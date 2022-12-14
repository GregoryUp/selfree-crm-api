<?php

class Clients
{

    private $conn;
    private $table_name = "clients";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function page($page) {
        $page = intval($page);
        
        $count = 0;

        $count = ($page - 1) * 10;

        $query = $this->conn->prepare("SELECT * FROM `{$this->table_name}` ORDER BY id LIMIT {$count}, 10");
        $query->execute();

        $rows = $query->fetchAll();

        return $rows;        
    }

    public function create($name, $surname, $lastname, $sex, $phone, $date_birth)
    {
        $query = $this->conn->prepare("INSERT INTO `{$this->table_name}` (lastname, name, surname, sex, date_birth, phone) VALUES(?, ?, ?, ?, ?, ?)");

        $query->execute([$lastname, $name, $surname, $sex, $date_birth, $phone]);
    }

    public function update($client_id, $data) {
        
        $client_id = intval($client_id);

        if($client_id === 0) return 'NO_CLIENT';

        $lastname = $data['lastname'];
        $name = $data['name'];
        $surname = $data['surname'];
        $sex = $data['sex'];
        $date_birth = $data['birth_date'];
        $phone = $data['phone'];

        $query = $this->conn->prepare("UPDATE `{$this->table_name}` SET lastname = '?', name = '?', surname = '?', sex = '?', date_birth = '?', phone = '?' WHERE id = '{$client_id}'");

        $query->execute([$lastname, $name, $surname, $sex, $date_birth, $phone]);
    }

    public function delete($client_id) {
        $client_id = intval($client_id);
        if($client_id === 0) return 'NO_CLIENT';

        $query = $this->conn->prepare("DELETE FROM `{$this->table_name}` WHERE id = '{$client_id}'");

        $query->execute();
    }
}
