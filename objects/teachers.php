<?php

class Teachers {

    private $pdo;
    private $table_name = 'teachers';

    public function __construct($db) {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    }

    public function __destruct(){
        $this->pdo = null;
    }

    public function read($teacher_id) {
        if(!filter_var($teacher_id, FILTER_VALIDATE_INT)) return 'NO_TEACHER';

        $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
        $error = $this->pdo->errorInfo();

        if(!empty($error[1])) return 'QUERY_FAILED';

        $query->execute(['id' => $teacher_id]);

        if ($query->rowCount() == 0) return 'NOT_FOUND';

        $teacher = $query->fetch(PDO::FETCH_ASSOC);

        return $teacher;
    }

    public function page($page, $per_page = 10) {
        if(!filter_var($page, FILTER_VALIDATE_INT)) return 'NO_PAGE';
        if(!filter_var($per_page, FILTER_VALIDATE_INT)) $per_page = 10;

        $count = ($page - 1) * $per_page;

        $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` ORDER BY id LIMIT :count, :per_page");
        $query->bindValue(':count', $count, PDO::PARAM_INT);
        $query->bindValue(':per_page', $per_page, PDO::PARAM_INT);
        $query->execute();

        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function getCountOfTeachers() {
        $query = $this->pdo->query("SELECT COUNT(*) AS 'count' FROM `{$this->table_name}`");
        $query->execute();

        $count = $query->fetchAll();

        return $count[0]['count'];
    }

    public function create($name, $gender, $skill, $email, $phone)
    {
        $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (name, gender, skill, email, phone) VALUES(?, ?, ?, ?, ?)");

        $query->execute([$name, $gender, $skill, $email, $phone]);
        return $this->pdo->lastInsertId();
    }

    public function update($teacher_id, $data) {
        if(!filter_var($teacher_id, FILTER_VALIDATE_INT)) return 'NO_TEACHER';

        $name = $data['name'];
        $gender = $data['gender'];
        $skill = $data['skill'];
        $email = $data['email'];
        $phone = $data['phone'];
    
        try {
            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET name = ?, gender = ?, skill = ?, email = ?, phone = ? WHERE id = ?");
            $query->execute([$name, $gender, $skill, $email, $phone, $teacher_id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED: " . $e->getMessage();
        }
    }

    public function delete($teacher_id) {
        if(!filter_var($teacher_id, FILTER_VALIDATE_INT)) return 'NO_CLIENT';
    
        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = :id");
            $query->bindValue(':id', $teacher_id, PDO::PARAM_INT);
            $query->execute();
            return $query->rowCount();
        } catch (PDOException $e) {
            return "QUERY_FAILED: " . $e->getMessage();
        }
    }
}