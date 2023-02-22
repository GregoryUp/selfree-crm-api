<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vendor/autoload.php';

require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/objects/lead_source_list.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];
$sourceListFields = json_decode(file_get_contents("php://input"), true);

if ($sourceListFields === null) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
}

if (empty($sourceListFields)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

$sourceList = new SourceList($db);

$sourceList_update_result = $sourceList->update($id, $sourceListFields);

if($sourceList_update_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$sourceList_update_result}"]));
}

if($sourceList_update_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$sourceList_update_result}"]));
}

echo 'OK';