<?php
require_once '../config/database.php';
require_once '../objects/clients.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];

$client = new Clients($db);

$client_deleteRegularLessons_result = $client->deleteRegularLessons($id);

if($client_deleteRegularLessons_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_deleteRegularLessons_result}"]));
}

if($client_deleteRegularLessons_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_deleteRegularLessons_result}"]));
}

echo 'OK';