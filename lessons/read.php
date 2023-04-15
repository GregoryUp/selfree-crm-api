<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/lessons.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];

header('Content-Type: application/json');

$lesson = new Lessons($db);

$lesson_read_result = $lesson->read($id);

if(gettype($lesson_read_result) == 'string') {
    if(strpos($lesson_read_result, 'ERROR_PARAMETER') !== false) {
        http_response_code(400);
        exit(json_encode(['error' => true, 'message' => "{$lesson_read_result}"]));
    }
    
    if ($lesson_read_result == 'NOT_FOUND') {
        http_response_code(404);
        exit(json_encode(['error' => true, 'message' => "{$lesson_read_result}"]));
    }
    
    if($lesson_read_result == 'QUERY_FAILED') {
        http_response_code(500);
        exit(json_encode(['error' => true, 'message' => "{$lesson_read_result}"]));
    }
}

echo json_encode($lesson_read_result);