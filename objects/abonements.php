<?php

class Abonements
{

    private $pdo;
    private $table_name = 'abonements';

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function create($abonement)
    {

        $name       = $abonement['name'];
        $tariff_id  = $abonement['tariff_id'];
        $duration   = $abonement['duration'];
        $price      = $abonement['price'];

        try {
            $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, tariff_id, duration, price) VALUES(?, ?, ?, ?)");
            $query->execute([$name, $tariff_id, $duration, $price]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function read($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        if ($id <= 0) return 'NOT_FOUND';

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

    public  function update($id, $data)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        $name = $data['name'];
        $tariff_id = $data['tariff_id'];
        $duration = $data['duration'];
        $price = $data['price'];

        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET name = ?, tariff_id = ?, duration = ?, price = ? WHERE id = ?");
            $query->execute([$name, $tariff_id, $duration, $price, $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED";
        }
    }

    public function delete($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        $id = intval($id);
        if ($id <= 0) return 'ERROR_PARAMETER';

        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = ?");
            $query->execute([$id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }
}
