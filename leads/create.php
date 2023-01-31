<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/leads.php';
include_once '../objects/source_list.php';
include_once '../functions/formatPhone.php';

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$db = new Database();
$db = $db->getConnection();

if (empty($_POST)) exit(json_encode(['status' => "Data is empty"]));

$name = $_POST['name'];
$phone = phone_format($_POST['phone']);
$email = $_POST['email'];
$comment = $_POST['comment'];
$source = $_POST['source'];
$status = $_POST['status'];

$phoneNumberObject = $phoneNumberUtil->parse($phone, 'RU');

if(!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    exit(json_encode(['schema' => 'Phone is not valid']));
}

if(!filter_var($status, FILTER_VALIDATE_INT)) {
    http_response_code(400);
    exit(json_encode(['schema' => 'Status is not valid']));
}

$sourceList = new SourceList($db);

$rows = $sourceList->getList();
$sources = array_column($rows, 'slug');

if(!in_array($source, $sources)) {
    http_response_code(400);
    exit(json_encode(['schema' => 'Such source doesn\'t exist']));
}

$lead = new Leads($db);

try {
    $lead->create($name, $phone, $email, $source, $status, $comment);
    echo 'OK';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}