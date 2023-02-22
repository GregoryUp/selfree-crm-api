<?php
define('DEBUG', true);

require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/leads.php';
require_once '../objects/lead_source_list.php';
require_once '../functions/formatPhone.php';

$db = new DataBase();
$db = $db->getConnection();

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$id = $_GET['id'];
$lead_fields = json_decode(file_get_contents("php://input"), true);
$lead_fields['phone'] = phone_format($lead_fields['phone']);

if ($lead_fields === null) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
}

if (empty($lead_fields)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}


$phoneNumberObject = $phoneNumberUtil->parse($lead_fields['phone'], 'RU');
if (!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Phone is not valid']));
}


$sourceList = new SourceList($db);

$rows = $sourceList->getList();
$sources = array_column($rows, 'slug');

if (!in_array($lead_fields['source'], $sources)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Source does not exist']));
}

$lead = new Leads($db);

$lead_update_result = $lead->update($id, $lead_fields);

if($lead_update_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$lead_update_result}"]));
}

if($lead_update_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$lead_update_result}"]));
}

echo 'OK';
