<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/lessons.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

if (empty($_POST)) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

$client_id  = $_POST['client_id'];
$teacher_id = $_POST['teacher_id'];
$type       = $_POST['type'];
$date       = $_POST['date'];
$time       = $_POST['time'];
$status_id  = $_POST['status_id'];
$subject_id = $_POST['subject_id'];
$comment    = $_POST['comment'];

$lesson = new Lessons($db);

$lesson_create_result = $lesson->create([
    'client_id' => $client_id,
    'teacher_id' => $teacher_id,
    'type' => $type,
    'date' => $date,
    'time' => $time,
    'status_id' => $status_id,
    'subject_id' => $subject_id,
    'comment' => $comment
]);

if(gettype($lesson_create_result) == 'string') {
    if(strpos($lesson_create_result, 'ERROR_PARAMETER') !== false) {
        http_response_code(400);
        exit(json_encode(['error' => true, 'message' => "{$lesson_create_result}"]));
    }

    if($lesson_create_result == 'QUERY_FAILED') {
        http_response_code(500);
        exit(json_encode(['error' => true, 'message' => "{$lesson_create_result}"]));
    }
}

echo json_encode(['success' => true]);