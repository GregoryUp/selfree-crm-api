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

        $client_id = $payment['client_id'];
        $date_create = date('Y-m-d H:i:s');
        $operation_type_id = intval($payment['operation_type_id']);
        $amount = doubleval($payment['amount']);
        $comment = $payment['comment'];

        try {

            $this->pdo->beginTransaction();

            $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (date_create, operation_type_id, amount, comment) VALUES(?, ?, ?, ?)");
            $query->execute([$date_create, $operation_type_id, $amount, $comment]);

            $payment_id = $this->pdo->lastInsertId();

            $query = $this->pdo->prepare("INSERT INTO `client_payments` SET client_id = ?, payment_id = ?, amount = ?");
            $query->execute([$client_id, $payment_id, $amount]);

            $query = $this->pdo->prepare("SELECT balance FROM `clients` WHERE id = ? ORDER BY id DESC LIMIT 1");
            $query->execute([$client_id]);

            $balance = $query->fetch(PDO::FETCH_ASSOC)['balance'];
            $balance += $amount;

            $query = $this->pdo->prepare("UPDATE `clients` SET balance = ? WHERE id = ?");
            $query->execute([$balance, $client_id]);

            $this->pdo->commit();

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
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
            return 'NOT_FOUND';

        $id = intval($id);
        if ($id <= 0) return 'NOT_FOUND';

        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = ?");
            $query->execute([$id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }
}
