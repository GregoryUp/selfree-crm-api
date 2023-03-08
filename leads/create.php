<?php
require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/leads.php';
require_once '../objects/lead_source_list.php';
require_once '../functions/formatPhone.php';

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$db = new Database();
$db = $db->getConnection();

if (empty($_POST)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Data is empty']));
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
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Phone is not valid']));
}

if (!filter_var($status, FILTER_VALIDATE_INT)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Status is not valid']));
}

$sourceList = new SourceList($db);
$rows = $sourceList->getList();
$sources = array_column($rows, 'slug');

if (!in_array($source, $sources)) {
    http_response_code(400);
    header('Content-Type: application/json');
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
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$lead_create_result}"]));
}

if($lead_create_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$lead_create_result}"]));
}

echo 'OK';