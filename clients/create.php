<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/clients.php';
include_once '../functions/formatPhone.php';

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$db = new Database();
$db = $db->getConnection();

if (empty($_POST))
    exit(json_encode(['status' => "Data is empty"]));

$surname = $_POST['surname'];
$name = $_POST['name'];
$middlename = $_POST['middlename'];
$gender = $_POST['gender'];
$phone = phone_format($_POST['phone']);
$date_birth = $_POST['date_birth'];

if (!in_array($gender, ['male', 'female'])) {
    http_response_code(403);
    exit(json_encode(['schema' => 'gender field must be \'male\' or \'female\'']));
}

$phoneNumberObject = $phoneNumberUtil->parse($phone, 'RU');

if(!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(403);
    exit(json_encode(['schema' => 'Phone is not valid']));
}

$client = new Clients($db);

try {
    $client->create($name, $surname, $middlename, $gender, $phone, $date_birth);
    echo 'OK';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}