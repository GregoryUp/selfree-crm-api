<?php
require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/lessons.php';

$db = new Database();
$db = $db->getConnection();

$client_id = $_POST['client_id'];

header('Content-Type: application/json');

$lesson = new Lessons($db);

$lesson_createClientTimetable_result = $lesson->createClientTimetable($client_id);

if(gettype($lesson_createClientTimetable_result) == 'string') {
    if(strpos($lesson_createClientTimetable_result, 'ERROR_PARAMETER') !== false) {
        http_response_code(400);
        exit(json_encode(['error' => true, 'message' => "{$lesson_createClientTimetable_result}"]));
    }

    if($lesson_createClientTimetable_result == 'QUERY_FAILED') {
        http_response_code(500);
        exit(json_encode(['error' => true, 'message' => "{$lesson_createClientTimetable_result}"]));
    }

    if($lesson_createClientTimetable_result == 'NOT_SET_REGULAR_LESSONS') {
        http_response_code(403);
        exit(json_encode(['error' => true, 'message' => "{$lesson_createClientTimetable_result}"]));
    }

    if($lesson_createClientTimetable_result == 'NOT_SET_ABONEMENT') {
        http_response_code(403);
        exit(json_encode(['error' => true, 'message' => "{$lesson_createClientTimetable_result}"]));
    }
}

echo json_encode($lesson_createClientTimetable_result);
