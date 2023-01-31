<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/teachers.php';
include_once '../functions/formatPhone.php';

$db = new Database();
$db = $db->getConnection();

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT) ? intval($_GET['id']) : die("ERROR_PARAMETER");
$json = file_get_contents("php://input");

$data = json_decode($json, true);

if($data === null) die("INVALID_JSON");
if(empty($data)) die("EMPTY_DATA");

$data['phone'] = phone_format($data['phone']);

if (!in_array($data['gender'], ['male', 'female'])) {
    http_response_code(400);
    exit(json_encode(['schema' => 'gender field must be \'male\' or \'female\'']));
}

$phoneNumberObject = $phoneNumberUtil->parse($data['phone'], 'RU');

if(!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    exit(json_encode(['schema' => 'Phone is not valid']));
}


$teacher = new Teachers($db);

try {
    $teacher->update($id, $data);
    echo 'OK';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}
