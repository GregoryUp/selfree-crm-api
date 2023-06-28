<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/lessons.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new Database();
$db = $db->getConnection();

$lesson = new Lessons($db);

$id = $_GET['id'];
$lesson_fields = json_decode(file_get_contents('php://input'), true);
verifyHttpBodyJSON($lesson_fields);

$status_id = $lesson_fields['status'];

if(!in_array($status_id, $lesson->statusList())) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "STATUS_NOT_EXIST"]));
}



header("Content-type: application/json");

echo json_encode(['success' => true]);