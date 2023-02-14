<?php

class Tariffs
{
    private $pdo;
    private $table_name = 'tariffs';

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function create($name, $duration, $price)
    {
        $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, duration, price) VALUES(?, ?, ?)");

        $query->execute([$name, $duration, $price]);

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
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'NO_TARIFF';

        $tariff_id = intval($id);
        if ($tariff_id == 0) return 'NO_FOUND';

        try {
            $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
            $query->execute(['id' => $tariff_id]);

            if ($query->rowCount() == 0) return 'NOT_FOUND';

            $row = $query->fetch(PDO::FETCH_ASSOC);

            return $row;
        } catch (PDOException $e) {
            http_response_code(500);
            return 'QUERY_FAILED';
        }
    }

    public function update($id, $data)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'NO_TARIFF';

        $name = $data['name'];
        $duration = $data['duration'];
        $price = $data['price'];

        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET name = ?, duration = ?, price = ? WHERE id = ?");
            $query->execute([$name, $duration, $price, $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED";
        }
    }

    public function delete($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT))
            return 'NO_TARIFF';

        $id = intval($id);
        if ($id <= 0) return 'NO_TARIFF';

        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = ?");
            $query->execute([$id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }
}
