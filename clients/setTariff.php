<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/settings.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/tariffs.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

$http_data = json_decode(file_get_contents("php://input"), true);

$client_id = intval($_GET['client_id']);
$tariff_id = intval($http_data['tariff_id']);
$subject_id = intval($http_data['subject_id']);

verifyHttpBodyJSON($http_data);

$clients = new Clients($db);
$tariffs = new Tariffs($db);
$subjects = new Settings($db, 'subjects');

$client = $clients->read($client_id);
$tariff = $tariffs->read($tariff_id);
$subject = $subjects->read($subject_id);

if($client == 'NOT_FOUND') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'NOT_FOUND_CLIENT']));
}

if($tariff == 'NOT_FOUND') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'NOT_FOUND_TARIFF']));
}

if($subject == 'NOT_FOUND') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'NOT_FOUND_SUBJECT']));
}

$clients_setTariff_result = $clients->setTariff($client_id, $tariff_id, $subject_id);

if(gettype($clients_setTariff_result) == 'string') {
    if(strpos($clients_setTariff_result, 'ERROR_PARAMETER') !== false) {
        http_response_code(400);
        exit(json_encode(['error' => true, 'message' => "{$clients_setTariff_result}"]));
    }

    if($clients_setTariff_result == 'QUERY_FAILED') {
        http_response_code(500);
        exit(json_encode(['error' => true, 'message' => "{$clients_setTariff_result}"]));
    }
}

echo json_encode(['success' => true]);