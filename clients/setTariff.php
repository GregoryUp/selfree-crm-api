<?php

require_once '../config/database.php';
require_once '../objects/clients.php';
require_once '../objects/tariffs.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];
$clientFieldTariffId = json_decode(file_get_contents("php://input"), true);

if ($clientFieldTariffId === null) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
}

if (empty($clientFieldTariffId['tariff_id'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

$client = new Clients($db);
$tariff = new Tariffs($db);


$tariff_read_result = $tariff->read($clientFieldTariffId['tariff_id']);

if ($tariff_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$tariff_read_result}"]));
}

if ($tariff_read_result == 'NOT_FOUND') {
    http_response_code(404);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Such tariff does not exist']));
}

if ($tariff_read_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$tariff_read_result}"]));
}

$client_setTariff_result = $client->setTariff($id, $clientFieldTariffId['tariff_id']);

if ($client_setTariff_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_setTariff_result}"]));
}

if ($client_setTariff_result == 'NOT_FOUND') {
    http_response_code(404);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "Such client does not exist"]));
}

if ($client_setTariff_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_setTariff_result}"]));
}

echo 'OK';
