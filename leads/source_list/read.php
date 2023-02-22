<?php

require_once $_SERVER['DOCUMENT_ROOT'] .'/vendor/autoload.php';

require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/objects/lead_source_list.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];

$sourceList = new SourceList($db);

$source_read_result = $sourceList->read($id);

if($source_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$source_read_result}"]));
}

if($source_read_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$source_read_result}"]));
}

if ($source_read_result == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => "{$source_read_result}"]));
}

echo json_encode($source_read_result);