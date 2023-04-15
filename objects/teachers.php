<?php

class Teachers
{

    private $pdo;
    private $table_name = 'teachers';

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function create($teacher)
    {
        if (!in_array($teacher['gender'], ['male', 'female'])) return 'ERROR_PARAMETER_GENDER';
        if (!is_array($teacher['subject_ids'])) return 'ERROR_PARAMETER_SUBJECT_IDS';

        $name = $teacher['name'];
        $gender = $teacher['gender'];
        $subject_ids = implode(',', $teacher['subject_ids']);
        $phone = $teacher['phone'];
        $email = $teacher['email'];

        try {
            $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, gender, subject_ids, email, phone) VALUES(?, ?, ?, ?, ?)");

            $query->execute([$name, $gender, $subject_ids, $email, $phone]);
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

            $teacher = $query->fetch(PDO::FETCH_ASSOC);

            return $teacher;
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function update($id, $data)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        $name = $data['name'];
        $gender = $data['gender'];
        $skill = $data['skill'];
        $email = $data['email'];
        $phone = $data['phone'];

        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET name = ?, gender = ?, skill = ?, email = ?, phone = ? WHERE id = ?");
            $query->execute([$name, $gender, $skill, $email, $phone, $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED";
        }
    }

    public function delete($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
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

    public function count()
    {
        try {
            $query = $this->pdo->query("SELECT COUNT(*) AS 'count' FROM `{$this->table_name}`");
            $query->execute();
            $count = $query->fetchAll();

            return $count[0]['count'];
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }
}
