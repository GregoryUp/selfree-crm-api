<?php

require_once '../config/database.php';
require_once '../objects/clients.php';
require_once '../objects/tariffs.php';

$db = new Database();
$db = $db->getConnection();

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT) ? intval($_GET['id']) : die("ERROR_PARAMETER");
$json = file_get_contents("php://input");

$data = json_decode($json, true);
$data['tariff_id'] = intval($data['tariff_id']);

if($data === null) {
    http_response_code(400);
    die("INVALID_JSON");
}

if(empty($data['tariff_id'])) {
    http_response_code(400);
    die("EMPTY_DATA");
}

$client = new Clients($db);
$tariff = new Tariffs($db);

try {
    $tariff = $tariff->read($data['tariff_id']);

    if ($tariff == 'NOT_FOUND') {
        http_response_code(404);
        exit($tariff);
    }

    $client = $client->setTariff($id, $data['tariff_id']);

    if($client == 'NOT_FOUND') {
        http_response_code(404);
        exit($client);
    }

    echo 'OK';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}