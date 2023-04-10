<?php

class Settings
{
    private $pdo;
    private $prefix = 'setting_';
    private $table_name = '';

    public function __construct($db, $table_name)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->table_name = $this->prefix . $table_name;

        $tableCheck = $this->isSettingExist();

        if ($tableCheck === 'QUERY_FAILED') {
            throw new Exception('QUERY_FAILED');
        }

        if(!$tableCheck) {
            throw new Exception('SETTING_NOT_EXIST');
        }
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function isSettingExist()
    {
        try {
            $query =  $this->pdo->prepare("SELECT TABLE_NAME AS 'name' FROM information_schema.tables WHERE table_schema = database() AND table_name = ?");
            $query->execute([$this->table_name]);
            $table = $query->fetch(PDO::FETCH_ASSOC);

            return !empty($table['name']) ? true : false;
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function create($setting)
    {
        $name = $setting['name'];
        $slug = $setting['slug'];

        if (empty($name) || empty($slug)) return 'ERROR_PARAMETER';

        try {

            $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, slug) VALUES(?, ?)");
            $query->execute([$name, $slug]);
            return $this->pdo->lastInsertId();

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

            $rows = $query->fetch(PDO::FETCH_ASSOC);
            return $rows;
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }

    }

    public function update($id, $setting)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        $name = $setting['name'];
        $slug = $setting['slug'];

        if (empty($name) || empty($slug)) return 'ERROR_PARAMETER';

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

    public function getList()
    {
        try {
            $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` ORDER BY id");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }
}