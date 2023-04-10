<?php
require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/teachers.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];

$teacher = new Teachers($db);

$teacher_delete_result = $teacher->delete($id);

if($teacher_delete_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$teacher_delete_result}"]));
}

if($teacher_delete_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$teacher_delete_result}"]));
}

echo json_encode(['success' => true]);