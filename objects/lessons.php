<?php
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';

class Lessons
{
    private $pdo;
    private $table_name = 'lessons';

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function createClientTimetable($client_id)
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_CLIENT_ID';

        $regularLessons = [];

        $client = new Clients($this->pdo);
        $client_getRegularLessons_result = $client->getRegularLessons($client_id);

        if (empty($client_getRegularLessons_result) && !is_array($client_getRegularLessons_result)) return 'NOT_SET_REGULAR_LESSONS';

        foreach ($client_getRegularLessons_result as $item) {
            if (!filter_var($item['teacher_id'], FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_TEAHCER_ID';
            if (!filter_var($item['subject_id'], FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_SUBJECT_ID';
            if ($item['date_start'] != date('Y-m-d', strtotime($item['date_start']))) return 'ERROR_PARAMETER_DATE';
            if ($item['date_end'] != date('Y-m-d', strtotime($item['date_end']))) return 'ERROR_PARAMETER_DATE';
            if (!($item['day_week'] >= 1 && $item['day_week'] <= 7)) return 'ERROR_PARAMETER_DAY';

            $regularLessons[] = [
                'teacher_id' => $item['teacher_id'],
                'subject_id' => $item['subject_id'],
                'day'        => $item['day_week'],
                'time'       => $item['time'],
                'date_start' => $item['date_start'],
                'date_end'   => $item['date_end']
            ];

            try {

                $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE client_id = :client_id AND subject_id = :subject_id AND date BETWEEN :date_start AND :date_end");
                $query->execute([
                    'client_id'     => $client_id,
                    'subject_id'    => $item['subject_id'],
                    'date_start'    => $item['date_start'],
                    'date_end'      => $item['date_end']
                ]);
            } catch (PDOException $e) {
                return 'QUERY_FAILED';
            }
        }

        try {
            $this->pdo->beginTransaction();

            foreach ($regularLessons as $regular) {
                $date_start = new DateTime($regular['date_start']);
                $date_end = new DateTime($regular['date_end']);

                $current_date = $date_start;

                while ($current_date <= $date_end) {
                    $day_of_week = $current_date->format('w');

                    if ($regular['day'] == $day_of_week) {

                        $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (client_id, teacher_id, type, date, time, status_id, subject_id, comment) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");

                        $query->execute([$client_id, $regular['teacher_id'], 'individual', $current_date->format('Y-m-d'), $regular['time'], '1', $regular['subject_id'], '']);
                    }

                    $current_date->modify('+1 day');
                }
            }

            $this->pdo->commit();

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return 'QUERY_FAILED';
        }
    }

    public function clientTimetable($client_id, $date_start = '', $date_end = '')
    {
        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';
        if (empty($date_start)) {
            $date_start = date('Y-m-d');
            $dateAt = strtotime('-1 month', strtotime($date_start));
            $date_start = date('Y-m-d', $dateAt);
        } else {
            $dateObject = DateTime::createFromFormat('Y-m-d', $date_start);
            if (!($dateObject !== false && $dateObject->format('Y-m-d') === $date_start)) {
                return 'ERROR_PARAMETER';
            }
        }

        if (empty($date_end)) {
            $date_end = date('Y-m-d');
            $dateAt = strtotime('+1 month', strtotime($date_end));
            $date_end = date('Y-m-d', $dateAt);
        } else {
            $dateObject = DateTime::createFromFormat('Y-m-d', $date_end);
            if (!($dateObject !== false && $dateObject->format('Y-m-d') === $date_end)) {
                return 'ERROR_PARAMETER';
            }
        }

        try {

            $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE client_id = :client_id AND date >= :date_start AND date <= :date_end ORDER BY date");
            $query->bindParam(':client_id', $client_id, PDO::PARAM_INT);
            $query->bindParam(':date_start', $date_start, PDO::PARAM_STR);
            $query->bindParam(':date_end', $date_end, PDO::PARAM_STR);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function create($lesson)
    {
        $client_id = $lesson['client_id'];
        $teacher_id = $lesson['teacher_id'];
        $type = $lesson['type'];
        $date = $lesson['date'];
        $time = $lesson['time'];
        $status_id = $lesson['status_id'];
        $subject_id = $lesson['subject_id'];
        $comment =  htmlspecialchars($lesson['comment']);

        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_CLIENT_ID';
        if (!filter_var($teacher_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_TEACHER_ID';
        if (!filter_var($status_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_STATUS_ID';
        if (!filter_var($subject_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_SUBJECT_ID';
        if (!in_array($type, ['individual'])) return 'ERROR_PARAMETER_TYPE';

        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if ($dateObj === false || $dateObj->format('Y-m-d') !== $date) return 'ERROR_PARAMETER_DATE';
        $timeObj = DateTime::createFromFormat('H:i', $time);
        if ($timeObj === false || $timeObj->format('H:i') !== $time) return 'ERROR_PARAMETER_TIME';

        try {

            $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (client_id, teacher_id, type, date, time, status_id, subject_id, comment) VALUES(:client_id, :teacher_id, :type, :date, :time, :status_id, :subject_id, :comment)");

            $query->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $query->bindValue(':teacher_id', $teacher_id, PDO::PARAM_INT);
            $query->bindValue(':type', $teacher_id, PDO::PARAM_STR);
            $query->bindValue(':date', $date, PDO::PARAM_STR);
            $query->bindValue(':time', $time, PDO::PARAM_STR);
            $query->bindValue(':status_id', $status_id, PDO::PARAM_INT);
            $query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
            $query->bindValue(':comment', $comment, PDO::PARAM_STR);

            $query->execute();

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function read($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_ID';

        try {
            $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE id = :id ORDER BY id DESC LIMIT 1");
            $query->execute(['id' => $id]);

            if ($query->rowCount() == 0) return 'NOT_FOUND';

            $lesson = $query->fetch(PDO::FETCH_ASSOC);

            return $lesson;
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function statusList() {
        try {
            $query = $this->pdo->prepare("SELECT * FROM `lesson_status_list`");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function update($id, $data)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_ID';

        $teacher_id = $data['teacher_id'];
        $type = $data['type'];
        $date = $data['date'];
        $time = $data['time'];
        $status_id = $data['status_id'];
        $subject_id = $data['subject_id'];
        $comment =  htmlspecialchars($data['comment']);

        if (!filter_var($teacher_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_TEACHER_ID';
        if (!filter_var($status_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_STATUS_ID';
        if (!filter_var($subject_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_SUBJECT_ID';
        if (!in_array($type, ['individual'])) return 'ERROR_PARAMETER_TYPE';
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if ($dateObj === false || $dateObj->format('Y-m-d') !== $date) return 'ERROR_PARAMETER_DATE';
        $timeObj = DateTime::createFromFormat('H:i', $time);
        if ($timeObj === false || $timeObj->format('H:i') !== $time) return 'ERROR_PARAMETER_TIME';

        try {

            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET teacher_id = :teacher_id, type = :type, date = :date, time = :time, status_id = :status_id, subject_id = :subject_id, comment = :comment WHERE id = :id");

            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->bindValue(':teacher_id', $teacher_id, PDO::PARAM_INT);
            $query->bindValue(':type', $type, PDO::PARAM_STR);
            $query->bindValue(':date', $date, PDO::PARAM_STR);
            $query->bindValue(':time', $time, PDO::PARAM_STR);
            $query->bindValue(':status_id', $status_id, PDO::PARAM_INT);
            $query->bindValue(':subject_id', $subject_id, PDO::PARAM_INT);
            $query->bindValue(':comment', $comment, PDO::PARAM_STR);

            $query->execute();

            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function setStatus($id, $status_id)
    {
        if(!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_LESSON_ID';
        if(!filter_var($status_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_STATUS_ID';

        try {

            $query = $this->pdo->prepare("UPDATE `{$this->table_name}` SET status_id = :status_id WHERE id = :id");
            $query->bindValue(':status_id', $status_id);
            $query->bindValue(':id', $id);
            $query->execute();
            return $query->rowCount();

        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function delete($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_ID';

        try {
            $query = $this->pdo->prepare("DELETE FROM `{$this->table_name}` WHERE id = ?");
            $query->execute([$id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }
}
