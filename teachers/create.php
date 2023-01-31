<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/teachers.php';
include_once '../functions/formatPhone.php';

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$db = new Database();
$db = $db->getConnection();

if (empty($_POST))
    exit(json_encode(['status' => "Data is empty"]));

$name = $_POST['name'];
$gender = $_POST['gender'];
$skill = $_POST['skill'];
$email = $_POST['email'];
$phone = phone_format($_POST['phone']);

if (!in_array($gender, ['male', 'female'])) {
    http_response_code(403);
    exit(json_encode(['schema' => 'gender field must be \'male\' or \'female\'']));
}

$phoneNumberObject = $phoneNumberUtil->parse($phone, 'RU');

if(!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(403);
    exit(json_encode(['schema' => 'Phone is not valid']));
}

$teacher = new Teachers($db);

try {
    $teacher->create($name, $gender, $skill, $email, $phone);
    echo 'OK';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}