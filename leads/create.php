<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/leads.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/lead_source_list.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/formatPhone.php';

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

if (empty($_POST)) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

$name = $_POST['name'];
$phone = phone_format($_POST['phone']);
$email = $_POST['email'];
$comment = $_POST['comment'];
$source = $_POST['source'];
$status = $_POST['status'];

$phoneNumberObject = $phoneNumberUtil->parse($phone, 'RU');

if (!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'Phone is not valid']));
}

if (!filter_var($status, FILTER_VALIDATE_INT)) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'Status is not valid']));
}

$sourceList = new SourceList($db);
$rows = $sourceList->getList();
$sources = array_column($rows, 'slug');

if (!in_array($source, $sources)) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'Source does not exist']));
}

$lead = new Leads($db);

$lead_create_result = $lead->create([
    'name'      => $name,
    'phone'     => $phone,
    'email'     => $email,
    'source'    => $source,
    'status'    => $status,
    'comment'   => $comment
]);

if($lead_create_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$lead_create_result}"]));
}

if($lead_create_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$lead_create_result}"]));
}

echo json_encode(['success' => true]);