<?php
include_once '../config/database.php';
include_once '../objects/clients.php';

$db = new Database();
$db = $db->getConnection();

$clients = new Clients($db);

try {
    $count = $clients->getCountOfClients();
    header('Content-Type: application/json');
    echo json_encode(["count" => $count]);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}
