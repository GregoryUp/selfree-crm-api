<?php

require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/leads.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];

$lead = new Leads($db);

$lead_delete_result = $lead->delete($id);

if($lead_delete_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$lead_delete_result}"]));
}

if($lead_delete_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$lead_delete_result}"]));
}

echo json_encode(['success' => true]);
