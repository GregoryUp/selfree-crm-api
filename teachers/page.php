<?php
require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/teachers.php';

$page = $_GET['page'];

$db = new Database();
$db = $db->getConnection();

$teacher = new Teachers($db);

$teacher_page_result = $teacher->page($page);

header('Content-Type: application/json');

if($teacher_page_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$teacher_page_result}"]));
}

if($teacher_page_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$teacher_page_result}"]));
}

echo json_encode($teacher_page_result);