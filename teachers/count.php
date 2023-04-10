<?php
require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/teachers.php';

$db = new Database();
$db = $db->getConnection();

$teachers = new Teachers($db);

$teacher_count_result = $teachers->count();

header('Content-Type: application/json');

if($teacher_count_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$teacher_count_result}"]));
}

echo json_encode(["count" => $teacher_count_result]);
