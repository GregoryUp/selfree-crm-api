<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/vendor/autoload.php';

require_once $_SERVER['DOCUMENT_ROOT'] .'/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/objects/lead_source_list.php';

$db = new Database();
$db = $db->getConnection();

if (empty($_POST)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Data is empty']));
}

$name = $_POST['name'];
$slug = $_POST['slug'];

$sourceList = new SourceList($db);

$sourceList_create_result = $sourceList->create([
    'name' => $name,
    'slug' => $slug
]);

if($sourceList_create_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$sourceList_create_result}"]));
}

if($sourceList_create_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$sourceList_create_result}"]));
}

echo 'OK';