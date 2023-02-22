<?php

class Clients
{

    private $pdo;
    private $table_name = "clients";

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function create($client)
    {

        $surname = $client['surname'];
        $name = $client['name'];
        $middlename = $client['middlename'];
        $gender = $client['gender'];
        $phone = $client['phone'];
        $date_birth = $client['date_birth'];

        try {
            $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (surname, name, middlename, gender, date_birth, phone) VALUES(?, ?, ?, ?, ?, ?)");

            $query->execute([$surname, $name, $middlename, $gender, $date_birth, $phone]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function read($client_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {
            $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
            $query->execute(['id' => $client_id]);

            if ($query->rowCount() == 0) return 'NOT_FOUND';

            $client = $query->fetch(PDO::FETCH_ASSOC);

            return $client;
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function update($client_id, $fields)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        $surname = $fields['surname'];
        $name = $fields['name'];
        $middlename = $fields['middlename'];
        $gender = $fields['gender'];
        $date_birth = $fields['date_birth'];
        $phone = $fields['phone'];

        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET surname = ?, name = ?, middlename = ?, gender = ?, date_birth = ?, phone = ? WHERE id = ?");
            $query->execute([$surname, $name, $middlename, $gender, $date_birth, $phone, $client_id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function delete($client_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = ?");
            $query->execute([$client_id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function page($page, $per_page = 10)
    {
        if (!filter_var($page, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';
        if (!filter_var($per_page, FILTER_VALIDATE_INT)) $per_page = 10;

        $count = ($page - 1) * $per_page;

        try {
            $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` ORDER BY id LIMIT :count, :per_page");
            $query->bindValue(':count', $count, PDO::PARAM_INT);
            $query->bindValue(':per_page', $per_page, PDO::PARAM_INT);
            $query->execute();

            $rows = $query->fetchAll(PDO::FETCH_ASSOC);

            return $rows;
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function getCount()
    {
        try {
            $query = $this->pdo->query("SELECT COUNT(*) AS 'count' FROM `{$this->table_name}`");
            $query->execute();
            $count = $query->fetchAll(PDO::FETCH_ASSOC);
            return $count[0]['count'];
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function setTariff($client_id, $tariff_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';
        if (!filter_var($tariff_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {

            $check_query = $this->pdo->prepare("SELECT id FROM `{$this->table_name}` WHERE id = :id");
            $check_query->bindValue(':id', $client_id);
            $check_query->execute();
            if ($check_query->rowCount() == 0) return 'NOT_FOUND';

            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET tariff_id = :tariff_id WHERE id = :id");
            $query->bindValue(':id', $client_id, PDO::PARAM_INT);
            $query->bindValue(':tariff_id', $tariff_id, PDO::PARAM_INT);
            $query->execute();
            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function setAbonement($client_id, $abonement_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';
        if (!filter_var($abonement_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {

            $check_client_query = $this->pdo->prepare("SELECT id FROM `{$this->table_name}` WHERE id = :id");
            $check_client_query->bindValue(':id', $client_id, PDO::PARAM_INT);
            $check_client_query->execute();
            if ($check_client_query->rowCount() == 0) return 'NOT_FOUND';

            $check_abonement_query = $this->pdo->prepare("SELECT id FROM `clients_abonements` WHERE client_id = :client_id AND abonement_id = :abonement_id");
            $check_abonement_query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $check_abonement_query->bindValue(':abonement_id', $abonement_id, PDO::PARAM_INT);
            $check_abonement_query->execute();
            if ($check_abonement_query->rowCount() > 0) {
                return $check_abonement_query->rowCount();
            } else {
                $query = $this->pdo->prepare("INSERT INTO `clients_abonements` SET client_id = :client_id, abonement_id = :abonement_id");
                $query->bindValue(':id', $client_id, PDO::PARAM_INT);
                $query->bindValue(':abonement_id', $abonement_id, PDO::PARAM_INT);
                $query->execute();
                return $query->rowCount();
            }
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }
}
