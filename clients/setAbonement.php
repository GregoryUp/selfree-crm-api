<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/abonements.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

$client_id = $_GET['client_id'];
$clientFieldAbonementId = json_decode(file_get_contents("php://input"), true);

verifyHttpBodyJSON($clientFieldAbonementId);

$client = new Clients($db);
$abonement = new Abonements($db);
$abonement_id = $clientFieldAbonementId['abonement_id'];

$abonement_read_result = $abonement->read($abonement_id);

if ($abonement_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$abonement_read_result}"]));
}

if ($abonement_read_result == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => 'Such abonement does not exist']));
}

if ($abonement_read_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$abonement_read_result}"]));
}

$client_setAbonement_result = $client->setAbonement($client_id, $abonement_id);

if ($client_setAbonement_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$client_setAbonement_result}"]));
}

if ($client_setAbonement_result == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => "Such client does not exist"]));
}

if ($client_setAbonement_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$client_setAbonement_result}"]));
}

echo json_encode(['success' => true]);