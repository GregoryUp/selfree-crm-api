<?php
require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/teachers.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];

$teacher = new Teachers($db);
$teacher_read_result = $teacher->read($id);

header('Content-Type: application/json');

if($teacher_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$teacher_read_result}"]));
}

if($teacher_read_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$teacher_read_result}"]));
}

if ($teacher_read_result == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => "{$teacher_read_result}"]));
}

echo json_encode($teacher_read_result);