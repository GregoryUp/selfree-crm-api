<?php

require_once '../config/database.php';
require_once '../objects/teachers.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];

$teacher = new Teachers($db);

$teacher_delete_result = $teacher->delete($id);

if($teacher_delete_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$teacher_delete_result}"]));
}

if($teacher_delete_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$teacher_delete_result}"]));
}

echo 'OK';