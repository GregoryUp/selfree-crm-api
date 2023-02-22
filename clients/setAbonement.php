<?php
require_once '../config/database.php';
require_once '../objects/clients.php';
require_once '../objects/abonements.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['client_id'];
$clientFieldAbonementId = json_decode(file_get_contents("php://input"), true);

if ($clientFieldAbonementId === null) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
}

if (empty($clientFieldAbonementId['abonement_id'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

$client = new Clients($db);
$abonement = new Abonements($db);


$abonement_read_result = $abonement->read($clientFieldAbonementId['abonement_id']);

if ($abonement_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$abonement_read_result}"]));
}

if ($abonement_read_result == 'NOT_FOUND') {
    http_response_code(404);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Such tariff does not exist']));
}

if ($abonement_read_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$abonement_read_result}"]));
}

$client_setAbonement_result = $client->setTariff($id, $clientFieldAbonementId['tariff_id']);

if ($client_setAbonement_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_setAbonement_result}"]));
}

if ($client_setAbonement_result == 'NOT_FOUND') {
    http_response_code(404);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "Such client does not exist"]));
}

if ($client_setAbonement_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_setAbonement_result}"]));
}

echo 'OK';
