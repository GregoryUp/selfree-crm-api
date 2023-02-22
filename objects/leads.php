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

    public function create($lead)
    {
        if(!filter_var($lead['status'], FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        $name = $lead['name'];
        $phone = $lead['phone'];
        $email = $lead['email'];
        $source = $lead['source'];
        $status = $lead['status'];
        $comment = $lead['comment'];

        $date_create = date('Y-m-d H:s:i');

        try {
            $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, phone, email, comment, source, date_create, status) VALUES(?, ?, ?, ?, ?, ?, ?)");
            $query->execute([$name, $phone, $email, $comment, $source, $date_create, $status]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function getList()
    {
        try {
            $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}`");

            $query->execute();
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);

            return $rows;
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function read($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {
            $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
            $query->execute(['id' => $id]);

            if ($query->rowCount() == 0) return 'NOT_FOUND';

            $row = $query->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function update($id, $data)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';
        if (!filter_var($data['status'], FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

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
            return 'QUERY_FAILED';
        }
    }

    public function setStatus($id, $status)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';
        if(!filter_var($status, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {

            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET status = :status WHERE id = :id");
            $query->bindValue(':status', $status, PDO::PARAM_INT);
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function delete($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = ?");
            $query->execute([$id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }
}
