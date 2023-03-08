<?php

require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/teachers.php';
require_once '../functions/formatPhone.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];

$teacher_fields = json_decode(file_get_contents("php://input"), true);

if ($teacher_fields === null) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
}

if (empty($teacher_fields)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

if (!in_array($teacher_fields['gender'], ['male', 'female'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'gender field must be \'male\' or \'female\'']));
}

if (!in_array($teacher_fields['gender'], ['male', 'female'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'gender field must be \'male\' or \'female\'']));
}

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
$teacher_fields['phone'] = phone_format($teacher_fields['phone']);
$phoneNumberObject = $phoneNumberUtil->parse($teacher_fields['phone'], 'RU');

if (!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Phone is not valid']));
}


$teacher = new Teachers($db);

$teacher_update_result = $teacher->update($id, $teacher_fields);

if($teacher_update_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$teacher_update_result}"]));
}

if($teacher_update_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$teacher_update_result}"]));
}

echo 'OK';