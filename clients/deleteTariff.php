<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

$client_id = $_GET['client_id'];
$tariff_id = $_GET['tariff_id'];
$subject_id = $_GET['subject_id'];

$clients = new Clients($db);

$clients_deleteTariff_result = $clients->deleteTariff($client_id, $tariff_id, $subject_id);

if(gettype($clients_deleteTariff_result) == 'string') {
    if(strpos($clients_deleteTariff_result, 'ERROR_PARAMETER') !== false) {
        http_response_code(400);
        exit(json_encode(['error' => true, 'message' => "{$clients_deleteTariff_result}"]));
    }

    if($clients_deleteTariff_result == 'QUERY_FAILED') {
        http_response_code(500);
        exit(json_encode(['error' => true, 'message' => "{$clients_deleteTariff_result}"]));
    }
}

echo json_encode(['success' => true]);