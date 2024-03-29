<?php
require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';

$db = new Database();
$db = $db->getConnection();

$clients = new Clients($db);

$client_count_result = $clients->getCount();

header('Content-Type: application/json');

if($client_count_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$client_count_result}"]));
}

echo json_encode(["count" => $client_count_result]);
