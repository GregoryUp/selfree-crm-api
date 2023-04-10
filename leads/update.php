<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/leads.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/settings.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/formatPhone.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$id = $_GET['id'];
$lead_fields = json_decode(file_get_contents("php://input"), true);

verifyHttpBodyJSON($lead_fields);

$lead_fields['phone'] = phone_format($lead_fields['phone']);

$phoneNumberObject = $phoneNumberUtil->parse($lead_fields['phone'], 'RU');
if (!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'Phone is not valid']));
}

$sourceList = new Settings($db, 'lead_source');

$rows = $sourceList->getList();
$sources = array_column($rows, 'slug');

if (!in_array($lead_fields['source'], $sources)) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'Source does not exist']));
}

$lead = new Leads($db);

$lead_update_result = $lead->update($id, $lead_fields);

if($lead_update_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$lead_update_result}"]));
}

if($lead_update_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$lead_update_result}"]));
}

echo json_encode(['success' => true]);
