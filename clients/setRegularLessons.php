<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/teachers.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/lessons.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

$client_id = $_GET['client_id'];
$clientFieldRegularLesson = json_decode(file_get_contents("php://input"), true);

verifyHttpBodyJSON($clientFieldRegularLesson);

$client = new Clients($db);
$teacher = new Teachers($db);
$lessons = new Lessons($db);

$client_read_result = $client->read($client_id);

if ($client_read_result == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => "CLIENT_NOT_FOUND"]));
}

if ($client_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "CLIENT_ERROR_PARAMETER"]));
}

$teacher_read_result = $teacher->read($clientFieldRegularLesson['teacher_id']);

if ($teacher_read_result == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => "TEACHER_NOT_FOUND"]));
}

if ($teacher_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "TEACHER_ERROR_PARAMETER"]));
}

$data = [];

$data['teacher_id'] = $clientFieldRegularLesson['teacher_id'];
$data['day_week'] = $clientFieldRegularLesson['day_week'];
$data['time'] = $clientFieldRegularLesson['time'];

$client_setRegularLesson_result = $client->setRegularLessons($client_id, $data);

if ($client_setRegularLesson_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$client_setRegularLesson_result}"]));
}

if ($client_setRegularLesson_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$client_setRegularLesson_result}"]));
}

$client_getAbonement_result = $client->getAbonements($client_id);

if (empty($lessons->clientTimetable($client_id, $client_getAbonement_result[0]['date_start'], $client_getAbonement_result[0]['date_end']))) {
    $lesson_createClientTimetable_result = $lessons->createClientTimetable($client_id);

    if (gettype($lesson_createClientTimetable_result) == 'string') {
        if (strpos($lesson_createClientTimetable_result, 'ERROR_PARAMETER') !== false) {
            http_response_code(400);
            exit(json_encode(['error' => true, 'message' => "{$lesson_createClientTimetable_result}"]));
        }

        if ($lesson_createClientTimetable_result == 'QUERY_FAILED') {
            http_response_code(500);
            exit(json_encode(['error' => true, 'message' => "{$lesson_createClientTimetable_result}"]));
        }

        if ($lesson_createClientTimetable_result == 'NOT_SET_REGULAR_LESSONS') {
            http_response_code(403);
            exit(json_encode(['error' => true, 'message' => "{$lesson_createClientTimetable_result}"]));
        }

        if ($lesson_createClientTimetable_result == 'NOT_SET_ABONEMENT') {
            http_response_code(403);
            exit(json_encode(['error' => true, 'message' => "{$lesson_createClientTimetable_result}"]));
        }
    }
}

echo json_encode(['success' => true]);
