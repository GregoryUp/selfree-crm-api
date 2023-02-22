<?php

require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/leads.php';

$db = new DataBase();
$db = $db->getConnection();

$id = $_GET['id'];

$lead = new Leads($db);

$lead_delete_result = $lead->delete($id);

if($lead_delete_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$lead_delete_result}"]));
}

if($lead_delete_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$lead_delete_result}"]));
}

echo 'OK';
