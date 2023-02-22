<?php
require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/clients.php';
require_once '../functions/formatPhone.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];
$client_fields = json_decode(file_get_contents("php://input"), true);;

if ($client_fields === null) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
}

if (empty($client_fields)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

if (!in_array($client_fields['gender'], ['male', 'female'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'gender field must be \'male\' or \'female\'']));
}

$client_fields['phone'] = phone_format($client_fields['phone']);
$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
$phoneNumberObject = $phoneNumberUtil->parse($client_fields['phone'], 'RU');

if (!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Phone is not valid']));
}


$client = new Clients($db);

$client_update_result = $client->update($id, $client_fields);

if($client_update_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_update_result}"]));
}

if($client_update_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$client_update_result}"]));
}

echo 'OK';
