<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];

$client = new Clients($db);

$client_read_result = $client->read($id);

header('Content-Type: application/json');

if($client_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$client_read_result}"]));
}

if ($client_read_result == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => "{$client_read_result}"]));
}

if($client_read_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$client_read_result}"]));
}

$client_getAbonements_result = $client->getAbonements($id);

$client_read_result['abonements'] = $client_getAbonements_result;

echo json_encode($client_read_result);
