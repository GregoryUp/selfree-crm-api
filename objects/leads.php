<?php

class Leads
{
    private $pdo;
    private $table_name = "leads";

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function create($name, $phone, $email, $source, $comment = '')
    {
        $date = date('Y-m-d H:s:i');

        $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, phone, email, comment, source, date) VALUES(?, ?, ?, ?, ?, ?)");

        $query->execute([$name, $phone, $email, $comment, $source, $date]);
    }

    public function getList()
    {
        $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}`");

        $rows = $query->execute()->fetchAll();

        return $rows;
    }

    public function read($id)
    {
        $lead_id = intval($id);
        if ($lead_id == 0) return 'NO_LEAD';

        $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
        $error = $this->pdo->errorInfo();

        if (!empty($error[1])) return 'QUERY_FAILED';

        $query->execute(['id' => $lead_id]);

        if ($query->rowCount() == 0) return 'LEAD_NOT_FOUND';

        $client = $query->fetch(PDO::FETCH_ASSOC);

        return $client;
    }

    public function update($id, $data)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'NO_LEAD';

        $name = $data['name'];
        $phone = $data['phone'];
        $email = $data['email'];
        $source = $data['source'];
        $comment = $data['comment'];

        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET name = ?, phone = ?, email = ?, source = ?, comment = ? WHERE id = ?");
            $query->execute([$name, $phone, $email, $source, $comment, $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED: " . $e->getMessage();
        }
    }
    public function delete($id)
    {
        $lead_id = intval($id);
        if ($lead_id === 0) return 'NO_LEAD';

        $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = '{$lead_id}'");

        $query->execute();
    }
}
