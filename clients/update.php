<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/client.php';
include_once '../functions/formatPhone.php';

$db = new Database();
$db = $db->getConnection();

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$id = intval($_GET['id']) != 0 ? intval($_GET['id']) : die("ERROR_PARAMETER");
$json = file_get_contents("php://input");

$data = json_decode($json, true);

if($data === null) die("INVALID_JSON");

$data['phone'] = phone_format($data['phone']);

if (!in_array($data['gender'], ['male', 'female'])) {
    http_response_code(403);
    exit(json_encode(['schema' => 'gender field must be \'male\' or \'female\'']));
}

$phoneNumberObject = $phoneNumberUtil->parse($data['phone'], 'RU');

if(!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(403);
    exit(json_encode(['schema' => 'Phone is not valid']));
}


$client = new Clients($db);

try {
    $client->update($id, $data);
    echo 'OK';
} catch (PDOException $e) {
    print_r($e);
    echo 'ERROR_REQUEST';
}
