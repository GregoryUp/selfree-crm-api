<?php

class Lessons {
    private $pdo;
    private $table_name = 'teachers';

    public function __construct($db) {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    }

    public function __destruct(){
        $this->pdo = null;
    }
}