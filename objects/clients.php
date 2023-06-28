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

        $dateObj = DateTime::createFromFormat('Y-m-d', $date_birth);
        if ($dateObj === false || $dateObj->format('Y-m-d') !== $date_birth) return 'ERROR_PARAMETER';

        try {
            $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (surname, name, middlename, gender, date_birth, phone) VALUES(?, ?, ?, ?, ?, ?)");

            $query->execute([$surname, $name, $middlename, $gender, $date_birth, $phone]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function read($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {
            $client_query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
            $client_query->execute(['id' => $id]);

            if ($client_query->rowCount() == 0) return 'NOT_FOUND';
            
            $client = $client_query->fetch(PDO::FETCH_ASSOC);

            // Подтягиваем тариф клиента
            $client_tariff_query = $this->pdo->prepare("SELECT ct.tariff_id, t.name, ct.subject_id, s.name AS 'subject_name'
                                                        FROM `clients_tariffs` AS ct
                                                        LEFT JOIN `tariffs` AS t ON ct.tariff_id = t.id
                                                        LEFT JOIN `setting_subjects` AS s ON ct.subject_id = s.id
                                                        WHERE ct.client_id = :client_id");
            $client_tariff_query->execute(['client_id' => $id]);
            $client_tariffs = $client_tariff_query->fetchAll(PDO::FETCH_ASSOC);

            // Подтягиваем абонемент клиента
            $client_abonement_query = $this->pdo->prepare("SELECT ca.abonement_id, a.name, ca.subject_id, s.name AS 'subject_name', ca.date_start, ca.date_end
                                                        FROM `clients_abonements` AS ca
                                                        LEFT JOIN `abonements` AS a ON ca.abonement_id = a.id
                                                        LEFT JOIN `setting_subjects` AS s ON ca.subject_id = s.id
                                                        WHERE ca.client_id = :client_id");
            $client_abonement_query->execute(['client_id' => $id]);
            $client_abonements = $client_abonement_query->fetchAll(PDO::FETCH_ASSOC);

            // Подтягиваем регулярный урок клиента
            $client_regularLessons_query = $this->pdo->prepare("SELECT crl.id, crl.teacher_id, crl.subject_id, t.name, crl.day_week, crl.time, crl.date_start, crl.date_end
                                                        FROM `client_regular_lessons` AS crl
                                                        LEFT JOIN `teachers` AS t ON crl.teacher_id = t.id
                                                        WHERE crl.client_id = :client_id");
            $client_regularLessons_query->execute(['client_id' => $id]);
            $client_regularLessons = $client_regularLessons_query->fetchAll(PDO::FETCH_ASSOC);

            

            $client['tariffs'] = $client_tariffs;
            $client['abonements'] = $client_abonements;
            $client['regular_lessons'] = $client_regularLessons;

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

    public function setTariff($client_id, $tariff_id, $subject_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_CLIENT_ID';
        if (!filter_var($tariff_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_TARIFF_ID';
        if (!filter_var($subject_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_SUBJECT_ID';

        try {

            $select_tariff_query = $this->pdo->prepare("SELECT * FROM `clients_tariffs` WHERE client_id = :client_id AND tariff_id = :tariff_id AND subject_id = :subject_id");
            $select_tariff_query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $select_tariff_query->bindValue(':tariff_id', $tariff_id, PDO::PARAM_INT);
            $select_tariff_query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
            $select_tariff_query->execute();

            if ($select_tariff_query->rowCount() > 0) return $select_tariff_query->fetch(PDO::FETCH_ASSOC)['id'];

            $query = $this->pdo->prepare("INSERT INTO `clients_tariffs` (client_id, tariff_id, subject_id) VALUES (:client_id, :tariff_id, :subject_id)");
            $query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $query->bindValue(':tariff_id', $tariff_id, PDO::PARAM_INT);
            $query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
            $query->execute();

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function deleteTariff($client_id, $tariff_id, $subject_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_CLIENT_ID';
        if (!filter_var($tariff_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_TARIFF_ID';
        if (!filter_var($subject_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_SUBJECT_ID';

        try {

            $query = $this->pdo->prepare("DELETE FROM `clients_tariffs` WHERE client_id = :client_id AND tariff_id = :tariff_id AND subject_id = :subject_id");

            $query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $query->bindValue(':tariff_id', $tariff_id, PDO::PARAM_INT);
            $query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
            $query->execute();

            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function setAbonement($client_id, $abonement_id, $subject_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_CLIENT_ID';
        if (!filter_var($abonement_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_ABONEMENT_ID';
        if (!filter_var($subject_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_SUBJECT_ID';

        try {
            $client_abonement_query = $this->pdo->prepare("SELECT id FROM `clients_abonements` WHERE client_id = :client_id AND abonement_id = :abonement_id AND subject_id = :subject_id");
            $client_abonement_query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $client_abonement_query->bindValue(':abonement_id', $abonement_id, PDO::PARAM_INT);
            $client_abonement_query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
            $client_abonement_query->execute();

            if ($client_abonement_query->rowCount() > 0) {
                return $client_abonement_query->rowCount();
            }

            $abonement_query = $this->pdo->prepare("SELECT * FROM `abonements` WHERE id = :id ORDER BY id DESC");
            $abonement_query->bindValue(':id', $abonement_id, PDO::PARAM_INT);
            $abonement_query->execute();

            if ($abonement_query->rowCount() == 0) return 'NOT_FOUND_ABONEMENT';

            $abonement = $abonement_query->fetch(PDO::FETCH_ASSOC);

            $date_start = date('Y-m-d');
            $date_end = date('Y-m-d', strtotime($date_start . ' +' . $abonement['duration'] . ' days'));

            $query = $this->pdo->prepare("INSERT INTO `clients_abonements` SET client_id = :client_id, abonement_id = :abonement_id, subject_id = :subject_id, date_start = :date_start, date_end = :date_end");
            $query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $query->bindValue(':abonement_id', $abonement_id, PDO::PARAM_INT);
            $query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
            $query->bindValue(':date_start', $date_start, PDO::PARAM_STR);
            $query->bindValue(':date_end', $date_end, PDO::PARAM_STR);
            $query->execute();

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function getAbonements($client_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {

            $query = $this->pdo->prepare("SELECT ca.*, a.subject_id FROM `clients_abonements` AS ca
            LEFT JOIN `abonements` AS a ON ca.abonement_id = a.id
            WHERE ca.client_id = :client_id ORDER BY ca.id DESC");
            $query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function deleteAbonement($client_id, $subject_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_CLIENT_ID';
        if (!filter_var($subject_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_SUBJECT_ID';

        try {

            $query = $this->pdo->prepare("DELETE FROM `clients_abonements` WHERE client_id = :client_id AND subject_id = :subject_id");
            $query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
            $query->execute();

            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function getRegularLessons($client_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {

            $query = $this->pdo->prepare("SELECT * FROM `client_regular_lessons` WHERE client_id = :client_id");
            $query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function setRegularLessons($client_id, $regularLesson)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_CLIENT_ID';
        if (!filter_var($regularLesson['subject_id'], FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_SUBJECT_ID';
        if (!($regularLesson['day_week'] <= 7 && $regularLesson['day_week'] >= 1)) return 'ERROR_PARAMETER_DAY_WEEK';

        $timestamp = strtotime($regularLesson['time']);
        if (!($timestamp !== false && date('H:i', $timestamp) === $regularLesson['time'])) return 'ERROR_PARAMETER_TIME';

        $date_start = strtotime($regularLesson['date_start']) !== false ? date('Y-m-d', strtotime($regularLesson['date_start'])) : date('Y-m-d');
        $date_end = strtotime($regularLesson['date_end']) !== false ? date('Y-m-d', strtotime($regularLesson['date_end'])) : date('Y-m-d');

        $subject_id = intval($regularLesson['subject_id']);

        try {

            $query = $this->pdo->prepare("SELECT * FROM `client_regular_lessons` WHERE client_id = :client_id AND subject_id = :subject_id AND day_week = :day_week");
            $query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
            $query->bindValue(':day_week', $regularLesson['day_week'], PDO::PARAM_INT);
            $query->execute();

            if (empty($query->rowCount())) {
                $query = $this->pdo->prepare("INSERT INTO `client_regular_lessons` (client_id, teacher_id, subject_id, day_week, time, date_start, date_end) VALUES (:client_id, :teacher_id, :subject_id, :day_week, :time, :date_start, :date_end)");
                $query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
                $query->bindValue(':teacher_id', $regularLesson['teacher_id'], PDO::PARAM_INT);
                $query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
                $query->bindValue(':day_week', $regularLesson['day_week'], PDO::PARAM_INT);
                $query->bindValue(':time', $regularLesson['time'], PDO::PARAM_STR);
                $query->bindValue(':date_start', $date_start, PDO::PARAM_STR);
                $query->bindValue(':date_end', $date_end, PDO::PARAM_STR);
                $query->execute();
                return $this->pdo->lastInsertId();
            } else {
                $query = $this->pdo->prepare("UPDATE `client_regular_lessons` SET teacher_id = :teacher_id, time = :time, date_start = :date_start, date_end = :date_end WHERE client_id = :client_id AND day_week = :day_week AND subject_id = :subject_id");
                $query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
                $query->bindValue(':teacher_id', $regularLesson['teacher_id'], PDO::PARAM_INT);
                $query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
                $query->bindValue(':day_week', $regularLesson['day_week'], PDO::PARAM_INT);
                $query->bindValue(':time', $regularLesson['time'], PDO::PARAM_STR);
                $query->bindValue(':date_start', $date_start, PDO::PARAM_STR);
                $query->bindValue(':date_end', $date_end, PDO::PARAM_STR);
                $query->execute();
                return $query->rowCount();
            }
        } catch (PDOException $e) {
            print_r($e->getMessage());
            return 'QUERY_FAILED';
        }
    }

    public function deleteRegularLessons($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

        try {
            $this->pdo->beginTransaction();

            $query = $this->pdo->prepare("SELECT * FROM `client_regular_lessons` WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            $regularLesson = $query->fetch(PDO::FETCH_ASSOC);

            $query = $this->pdo->prepare("DELETE FROM `lessons` WHERE client_id = :client_id AND date BETWEEN :date_start AND :date_end AND subject_id = :subject_id AND WEEKDAY(date) + 1 = :day_week AND status_id IN(1,3,5)");
            $query->bindValue(':client_id', $regularLesson['client_id'], PDO::PARAM_INT);
            $query->bindValue(':date_start', $regularLesson['date_start'], PDO::PARAM_STR);
            $query->bindValue(':date_end', $regularLesson['date_end'], PDO::PARAM_STR);
            $query->bindValue(':subject_id', $regularLesson['subject_id'], PDO::PARAM_INT);
            $query->bindValue(':day_week', $regularLesson['day_week'], PDO::PARAM_INT);
            $query->execute();

            $query = $this->pdo->prepare("DELETE FROM `client_regular_lessons` WHERE id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            $this->pdo->commit();
            return $query->rowCount();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return 'QUERY_FAILED';
        }
    }
}
