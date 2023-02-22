<?php

require_once '../config/database.php';
require_once '../objects/clients.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];

$client = new Clients($db);

$client_delete_result = $client->delete($id);

if($client_delete_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_delete_result}"]));
}

if($client_delete_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_delete_result}"]));
}

echo 'OK';
