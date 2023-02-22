<?php

class SourceList
{
    private $pdo;
    private $table_name = "lead_source_list";

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function create($source)
    {

        $name = $source['name'];
        $slug = $source['slug'];

        if (empty($name) || empty($slug)) return 'ERROR_PARAMETER';

        try {
            $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, slug) VALUES(?, ?)");
            $query->execute([$name, $slug]);
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

            $source = $query->fetch(PDO::FETCH_ASSOC);
            return $source;
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function update($id, $data)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        $name = $data['name'];
        $slug = $data['slug'];

        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET name = ?, slug = ? WHERE id = ?");
            $query->execute([$name, $slug, $id]);
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
