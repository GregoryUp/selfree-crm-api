<?php

require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/leads.php';

$db = new DataBase();
$db = $db->getConnection();

$id = $_GET['id'];

$leadFieldStatusId = json_decode(file_get_contents("php://input"), true);

if ($leadFieldStatusId === null) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
}

if (empty($leadFieldStatusId['status'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

$lead = new Leads($db);

$lead_setStatus_result = $lead->setStatus($id, $leadFieldStatusId['status']);

if($lead_setStatus_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$lead_setStatus_result}"]));
}

if($lead_setStatus_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$lead_setStatus_result}"]));
}

echo 'OK';
