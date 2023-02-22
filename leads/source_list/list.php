<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vendor/autoload.php';

require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/objects/lead_source_list.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$sourceList = new SourceList($db);

$sourceList_getList_result = $sourceList->getList();

if($sourceList_getList_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$sourceList_getList_result}"]));
}
    
echo json_encode($sourceList_getList_result);
