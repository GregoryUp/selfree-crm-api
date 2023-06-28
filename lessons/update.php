<?php
require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/lessons.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];
$lesson_fields = json_decode(file_get_contents('php://input'), true);

verifyHttpBodyJSON($lesson_fields);

$lesson = new Lessons($db);

$lesson_update_result = $lesson->update($id, $lesson_fields);

if(gettype($lesson_update_result) == 'string') {
    if(strpos($lesson_update_result, 'ERROR_PARAMETER') !== false) {
        http_response_code(400);
        exit(json_encode(['error' => true, 'message' => "{$lesson_update_result}"]));
    }

    if($lesson_update_result == 'NOT_FOUND') {
        http_response_code(404);
        exit(json_encode(['error' => true, 'message' => "{$lesson_update_result}"]));
    }

    if($lesson_update_result == 'QUERY_FAILED') {
        http_response_code(500);
        exit(json_encode(['error' => true, 'message' => "{$lesson_update_result}"]));
    }
}

echo json_encode(['success' => true]);