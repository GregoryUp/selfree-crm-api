<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/leads.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];

$leadFieldStatusId = json_decode(file_get_contents("php://input"), true);

verifyHttpBodyJSON($leadFieldStatusId);

$lead = new Leads($db);

$lead_setStatus_result = $lead->setStatus($id, $leadFieldStatusId['status']);

if($lead_setStatus_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$lead_setStatus_result}"]));
}

if($lead_setStatus_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$lead_setStatus_result}"]));
}

echo json_encode(['success' => true]);
