<?php

class Abonements {
    
    private $pdo;
    private $table_name = 'abonements';

    public function __construct($db) {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function create($name, $tariff_id, $price) {
        $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, tariff_id, price) VALUES(?, ?, ?)");

        $query->execute([$name, $tariff_id, $price]);

        return $this->pdo->lastInsertId();
    }

    public function read($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'NO_ABONEMENT';

        $id = intval($id);

        if ($id <= 0) return 'NO_ABONEMENT';

        $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
        $error = $this->pdo->errorInfo();

        if (!empty($error[1])) return 'QUERY_FAILED';

        $query->execute(['id' => $id]);

        if ($query->rowCount() == 0) return 'NOT_FOUND';

        $row = $query->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public function getList() {
        $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}`");
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public  function update($id, $data) {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'NO_ABONEMENT';

        $name = $data['name'];
        $tariff_id = $data['tariff_id'];
        $price = $data['price'];

        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET name = ?, tariff_id = ?, price = ? WHERE id = ?");
            $query->execute([$name, $tariff_id, $price, $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED";
        }
    }

    public function delete($id) {
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