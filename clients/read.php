<?php

require_once '../config/database.php';
require_once '../objects/clients.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];

$client = new Clients($db);

$client = $client->read($id);

header('Content-Type: application/json');

if($client == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$client}"]));
}

if($client == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$client}"]));
}

if ($client == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => "{$client}"]));
}

echo json_encode($client);
