<?php

class Clients
{

    private $conn;
    private $table_name = "clients";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function view($client_id) {
        $client_id = intval($client_id);
        if($client_id === 0) return 'NO_CLIENT';

        $query = $this->conn->prepare("SELECT * FROM `{$this->table_name}` id = ':id' ORDER BY id DESC LIMIT 1");
        $query->execute(['id' => $client_id]);

        $client = $query->fetch();

        return $client;
    }

    public function page($page, $per_page = 10) {
        $page = intval($page);
        $per_page = intval($per_page) != 0 ? intval($per_page) : 10; 
        
        $count = 0;

        $count = ($page - 1) * $per_page;

        $query = $this->conn->prepare("SELECT * FROM `{$this->table_name}` ORDER BY id LIMIT {$count}, {$per_page}");
        $query->execute();

        $rows = $query->fetchAll();

        return $rows;        
    }

    public function getCountOfClients() {
        $query = $this->conn->query("SELECT COUNT(*) AS 'count' FROM `{$this->table_name}`");
        $query->execute();

        $count = $query->fetchAll();

        return $count[0]['count'];
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
