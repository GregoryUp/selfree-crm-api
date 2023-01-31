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

    public function create($name, $phone, $email, $source, $status, $comment = '')
    {
        $date_create = date('Y-m-d H:s:i');

        $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, phone, email, comment, source, date_create, status) VALUES(?, ?, ?, ?, ?, ?, ?)");

        $query->execute([$name, $phone, $email, $comment, $source, $date_create, $status]);

        return $this->pdo->lastInsertId();
    }

    public function getList()
    {
        $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}`");

        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function read($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'NO_LEAD';

        $lead_id = intval($id);

        if ($lead_id == 0) return 'NO_LEAD';

        $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
        $error = $this->pdo->errorInfo();

        if (!empty($error[1])) return 'QUERY_FAILED';

        $query->execute(['id' => $lead_id]);

        if ($query->rowCount() == 0) return 'NOT_FOUND';

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
        $status = $data['status'];
        $comment = $data['comment'];

        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET name = ?, phone = ?, email = ?, source = ?, status = ?, comment = ? WHERE id = ?");
            $query->execute([$name, $phone, $email, $source, $status, $comment, $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED";
        }
    }

    public function setStatus($id, $status)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'NO_LEAD';

        try {

            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET status = :status WHERE id = :id");
            $query->bindValue(':status', $status, PDO::PARAM_INT);
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT))
            return 'NO_LEAD';

        $lead_id = intval($id);
        if ($lead_id <= 0) return 'NO_LEAD';

        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = ?");
            $query->execute([$lead_id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }
}
