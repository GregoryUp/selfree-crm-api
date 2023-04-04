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

        $client = new Clients($this->pdo);

        $regularLessons = [];

        $client_getRegularLessons_result = $client->getRegularLessons($client_id);
        $client_getAbonement_result = $client->getAbonements($client_id);

        $lessonsDays = [];

        foreach ($client_getRegularLessons_result as $item) {
            $lessonsDays[] = [
                'day'   => $item['day_week'],
                'time'  => $item['time'],
                'teacher_id' => $item['teacher_id']
            ];
        }


        $regularLessons = [
            'date_start' => $client_getAbonement_result[0]['date_start'],
            'date_end'   => $client_getAbonement_result[0]['date_end'],
            'subject_id' => $client_getAbonement_result[0]['subject_id'],
            'lessonsDays' => $lessonsDays
        ];


        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';
        if (!is_array($regularLessons['lessonsDays']) || count($regularLessons['lessonsDays']) == 0) return 'ERROR_PARAMETER';

        $date_start = $regularLessons['date_start'];
        $date_end = $regularLessons['date_end'];
        $subject_id = $regularLessons['subject_id'];

        if ($date_start != date('Y-m-d', strtotime($date_start))) return 'ERROR_PARAMETER';
        if ($date_end != date('Y-m-d', strtotime($date_end))) return 'ERROR_PARAMETER';

        $lessonsDays = [];

        foreach ($regularLessons['lessonsDays'] as $lessonDay) {
            if (!($lessonDay['day'] >= 1 && $lessonDay['day'] <= 7)) return 'ERROR_PARAMETER';
            $timestamp = strtotime($lessonDay['time']);
            $time_arr = explode(':', $lessonDay['time']);
            $time = $time_arr[0] . ':' . $time_arr[1];
            if (!($timestamp !== false && date('H:i', $timestamp) === $time)) return 'ERROR_PARAMETER';

            $lessonsDays[] = [
                'day'   => $lessonDay['day'],
                'time'  => $time,
                'teacher_id' => $lessonDay['teacher_id'],
            ];
        }

        $lessons_timetable = [];

        $date_start = new DateTime($regularLessons['date_start']);
        $date_end = new DateTime($regularLessons['date_end']);

        $current_date = $date_start;

        try {
            $this->pdo->beginTransaction();

            while ($current_date <= $date_end) {
                $day_of_week = $current_date->format('w');

                foreach ($lessonsDays as $lesson) {
                    if ($lesson['day'] == $day_of_week) {
                        $lessons_timetable[] = [
                            'date' => $current_date->format('Y-m-d'),
                            'teacher_id' => $lesson['teacher_id'],
                            'time' => $lesson['time'],
                            'status_id' => 1,
                            'subject_id' => $subject_id,
                        ];

                        $query = $this->pdo->prepare("INSERT INTO `{$this->table_name}` (client_id, teacher_id, type, date, time, status_id, subject_id, comment) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");

                        $query->execute([$client_id, $lesson['teacher_id'], 'individual', $current_date->format('Y-m-d'), $lesson['time'], '1', $subject_id, '']);
                    }
                }
                $current_date->modify('+1 day');
            }

            $this->pdo->commit();

            return $lessons_timetable;
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
        } else {
            $dateObject = DateTime::createFromFormat('Y-m-d', $date_start);
            if(!($dateObject !== false && $dateObject->format('Y-m-d') === $date_start)) {
                return 'ERROR_PARAMETER';
            }

        }

        if (empty($date_end)) {
            $dateAt = strtotime('+1 MONTH', strtotime($date_end));
            $date_end = date('Y-m-d', $dateAt);
        } else {
            $dateObject = DateTime::createFromFormat('Y-m-d', $date_end);
            if(!($dateObject !== false && $dateObject->format('Y-m-d') === $date_end)) {
                return 'ERROR_PARAMETER';
            }
        }

        try {

            $query = $this->pdo->prepare("SELECT * FROM `{$this->table_name}` WHERE client_id = :client_id AND date >= :date_start AND date <= :date_end");
            $query->bindParam(':client_id', $client_id, PDO::PARAM_INT);
            $query->bindParam(':date_start', $date_start, PDO::PARAM_STR);
            $query->bindParam(':date_end', $date_end, PDO::PARAM_STR);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return 'QUERY_FAILED';
        }
    }

    public function create($lesson) {
        $client_id = $lesson['client_id'];
        $teacher_id = $lesson['teacher_id'];
        $type = $lesson['type'];
        $date = $lesson['date'];
        $time = $lesson['time'];
        $status_id = $lesson['status_id'];
        $subject_id = $lesson['subject_id'];
        $comment =  $lesson['comment'];

        if (!filter_var($client_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_CLIENT_ID';
        if (!filter_var($teacher_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_TEACHER_ID';
        if (!filter_var($status_id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER_';
    }

    public function read($id) {
        if(!filter_var($id, FILTER_VALIDATE_INT)) return 'ERROR_PARAMETER';

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

}
