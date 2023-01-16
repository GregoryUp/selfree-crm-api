<?php

class Clients
{

    private $pdo;
    private $table_name = "clients";

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    }

    public function __destruct(){
        $this->pdo = null;
    }

    public function read($client_id) {
        if(!filter_var($client_id, FILTER_VALIDATE_INT)) return 'NO_CLIENT';

        $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
        $error = $this->pdo->errorInfo();

        if(!empty($error[1])) return 'QUERY_FAILED';

        $query->execute(['id' => $client_id]);

        $client = $query->fetch(PDO::FETCH_ASSOC);

        return $client;
    }

    public function page($page, $per_page = 10) {
        if(!filter_var($page, FILTER_VALIDATE_INT)) return 'NO_PAGE';
        if(!filter_var($per_page, FILTER_VALIDATE_INT)) $per_page = 10;

        $count = ($page - 1) * $per_page;

        $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` ORDER BY id LIMIT :count, :per_page");
        $query->bindValue(':count', $count, PDO::PARAM_INT);
        $query->bindValue(':per_page', $per_page, PDO::PARAM_INT);
        $query->execute();

        $rows = $query->fetchAll();

        return $rows;        
    }

    public function getCountOfClients() {
        $query = $this->pdo->query("SELECT COUNT(*) AS 'count' FROM `{$this->table_name}`");
        $query->execute();

        $count = $query->fetchAll();

        return $count[0]['count'];
    }

    public function create($name, $surname, $middlename, $gender, $phone, $date_birth)
    {
        $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (surname, name, middlename, gender, date_birth, phone) VALUES(?, ?, ?, ?, ?, ?)");

        $query->execute([$surname, $name, $middlename, $gender, $date_birth, $phone]);
        return $this->pdo->lastInsertId();
    }

    public function update($client_id, $data) {
        if(!filter_var($client_id, FILTER_VALIDATE_INT)) return 'NO_CLIENT';
    
        $surname = $data['surname'];
        $name = $data['name'];
        $middlename = $data['middlename'];
        $gender = $data['gender'];
        $date_birth = $data['date_birth'];
        $phone = $data['phone'];
    
        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET surname = ?, name = ?, middlename = ?, gender = ?, date_birth = ?, phone = ? WHERE id = ?");
            $query->execute([$surname, $name, $middlename, $gender, $date_birth, $phone, $client_id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED: " . $e->getMessage();
        }
    }

    public function delete($client_id) {
        if(!filter_var($client_id, FILTER_VALIDATE_INT)) return 'NO_CLIENT';
    
        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = ?");
            $query->execute([$client_id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED: " . $e->getMessage();
        }
    }
}
