<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';

$page = $_GET['page'];

$db = new Database();
$db = $db->getConnection();

$client = new Clients($db);

$client_page_result = $client->page($page);

header('Content-Type: application/json');

if($client_page_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$client_page_result}"]));
}

if($client_page_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$client_page_result}"]));
}

echo json_encode($client_page_result);
