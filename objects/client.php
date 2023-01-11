<?php

class Clients
{

    private $conn;
    private $table_name = "clients";

    public function __construct($db)
    {
        $this->conn = $db;
        $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    }

    public function view($client_id) {
        $client_id = intval($client_id);
        if($client_id == 0) return 'NO_CLIENT';

        $query = $this->conn->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
        $error = $this->conn->errorInfo();

        if(!empty($error[1])) return 'QUERY_FAILED';

        $query->execute(['id' => $client_id]);

        $client = $query->fetch(PDO::FETCH_ASSOC);

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

    public function create($name, $surname, $middlename, $gender, $phone, $date_birth)
    {
        $query = $this->conn->prepare("INSERT INTO `{$this->table_name}` (surname, name, middlename, gender, date_birth, phone) VALUES(?, ?, ?, ?, ?, ?)");

        $query->execute([$surname, $name, $middlename, $gender, $date_birth, $phone]);
    }

    public function update($client_id, $data) {
        
        $client_id = intval($client_id);

        if($client_id === 0) return 'NO_CLIENT';

        $surname = $data['surname'];
        $name = $data['name'];
        $middlename = $data['middlename'];
        $gender = $data['gender'];
        $date_birth = $data['date_birth'];
        $phone = $data['phone'];

        $query = $this->conn->prepare("UPDATE `{$this->table_name}` SET surname = ?, name = ?, middlename = ?, gender = ?, date_birth = ?, phone = ? WHERE id = '{$client_id}'");

        $query->execute([$surname, $name, $middlename, $gender, $date_birth, $phone]);
    }

    public function delete($client_id) {
        $client_id = intval($client_id);
        if($client_id === 0) return 'NO_CLIENT';

        $query = $this->conn->prepare("DELETE FROM `{$this->table_name}` WHERE id = '{$client_id}'");

        $query->execute();
    }
}
