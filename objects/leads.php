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

    public function getList() {
        $query = $this->conn->prepare("SELECT * FROM `{$this->table_name}`");

        $rows = $query->execute()->fetchAll();

        return $rows;
    }

    public function view($id) {
        $lead_id = intval($id);
        if($lead_id == 0) return 'NO_LEAD';

        $query = $this->conn->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
        $error = $this->conn->errorInfo();

        if(!empty($error[1])) return 'QUERY_FAILED';

        $query->execute(['id' => $lead_id]);

        if($query->rowCount() == 0) return 'LEAD_NOT_FOUND';

        $client = $query->fetch(PDO::FETCH_ASSOC);

        return $client;
    }

    public function update($id) {

    } 

    public function delete($id) {
        $lead_id = intval($id);
        if($lead_id === 0) return 'NO_LEAD';

        $query = $this->conn->prepare("DELETE FROM `{$this->table_name}` WHERE id = '{$lead_id}'");

        $query->execute();
    }

}