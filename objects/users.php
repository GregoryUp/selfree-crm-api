<?php

class Users {

    private $pdo;
    private $table_name = "users";

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }


    public function auth($login, $password) {

        try {

            $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE login = ?");
            $query->execute([$login]);

            if($query->rowCount() == 0) return 'AUTH_FALSE';

            $user = $query->fetch(PDO::FETCH_ASSOC);

            if($user['password'] == $password) return 'AUTH_TRUE';

            return 'AUTH_FALSE';

        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }

    }

}