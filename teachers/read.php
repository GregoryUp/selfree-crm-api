<?php

require_once '../config/database.php';
require_once '../objects/teachers.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];

$teacher = new Teachers($db);
$teacher = $teacher->read($id);

header('Content-Type: application/json');

if($teacher == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$teacher}"]));
}

if($teacher == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$client}"]));
}

if ($teacher == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => "{$teacher}"]));
}

echo json_encode($teacher);