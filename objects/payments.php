<?php

class Payments
{

    private $pdo;
    private $table_name = 'payments';

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function create($payment)
    {
        try {
            $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (client_id, date_create, operation_type_id, sum) VALUES(?, ?, ?, ?)");

            $query->execute([$payment['client_id'], $payment['date_create'], $payment['operation_type_id'], $payment['sum']]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function read($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        $id = intval($id);

        if ($id <= 0) return 'ERROR_PARAMETER';

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

        $client_id = $data['client_id'];
        $date_create = $data['date_create'];
        $operation_type_id = $data['operation_type_id'];
        $sum = $data['sum'];

        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET client_id = ?, date_create = ?, operation_type_id = ?, sum = ? WHERE id = ?");
            $query->execute([$client_id, $date_create, $operation_type_id, $sum, $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED";
        }
    }

    public function delete($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT))
            return 'NO_ABONEMENT';

        $id = intval($id);
        if ($id <= 0) return 'NO_ABONEMENT';

        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = ?");
            $query->execute([$id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }
}
