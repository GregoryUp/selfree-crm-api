<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/leads.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$lead = new Leads($db);

$lead_getList_result = $lead->getList();

if($lead_create_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$lead_create_result}"]));
}

echo json_encode($lead_getList_result);