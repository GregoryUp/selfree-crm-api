<?php

require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/formatPhone.php';

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

if (empty($_POST)) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'Data is empty']));
}

$surname = $_POST['surname'];
$name = $_POST['name'];
$middlename = $_POST['middlename'];
$gender = $_POST['gender'];
$phone = phone_format($_POST['phone']);
$date_birth = $_POST['date_birth'];

if (!in_array($gender, ['male', 'female'])) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'gender field must be \'male\' or \'female\'']));
}

$phoneNumberObject = $phoneNumberUtil->parse($phone, 'RU');

if (!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'Phone is not valid']));
}

$client = new Clients($db);


$client_create_result = $client->create([
    'surname'    => $surname,
    'name'       => $name,
    'middlename' => $middlename,
    'gender'     => $gender,
    'phone'      => $phone,
    'date_birth' => $date_birth
]);

if($client_create_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$client_create_result}"]));
}

if($client_create_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$client_create_result}"]));
}

echo json_encode(['success' => true]);