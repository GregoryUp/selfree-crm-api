<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/teachers.php';

$db = new Database();
$db = $db->getConnection();

$client_id = $_GET['client_id'];
$clientFieldRegularLesson = json_decode(file_get_contents("php://input"), true);

if ($clientFieldRegularLesson === null) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
}

if (empty($clientFieldRegularLesson)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

$client = new Clients($db);
$teacher = new Teachers($db);

$client_read_result = $client->read($client_id);

if($client_read_result == 'NOT_FOUND') {
    http_response_code(404);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "CLIENT_NOT_FOUND"]));
}

if($client_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "CLIENT_ERROR_PARAMETER"]));
}

$teacher_read_result = $teacher->read($clientFieldRegularLesson['teacher_id']);

if($teacher_read_result == 'NOT_FOUND') {
    http_response_code(404);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "TEACHER_NOT_FOUND"]));
}

if($teacher_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "TEACHER_ERROR_PARAMETER"]));
}

$data = [];

$data['teacher_id'] = $clientFieldRegularLesson['teacher_id'];
$data['day_week'] = $clientFieldRegularLesson['day_week'];
$data['time'] = $clientFieldRegularLesson['time'];

$client_setRegularLesson_result = $client->setRegularLessons($client_id, $data);

if ($client_setRegularLesson_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_setRegularLesson_result}"]));
}

if ($client_setRegularLesson_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_setRegularLesson_result}"]));
}

echo 'OK';