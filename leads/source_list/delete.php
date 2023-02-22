<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/objects/lead_source_list.php';

$db = new Database();
$db = $db->getConnection();

$id = intval($_GET['id']);

$sourceList = new SourceList($db);
$sourceList_delete_result = $sourceList->delete($id);

if($sourceList_delete_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$sourceList_delete_result}"]));
}

if($sourceList_delete_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$sourceList_delete_result}"]));
}

echo 'OK';