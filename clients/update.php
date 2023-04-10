<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/formatPhone.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new Database();
$db = $db->getConnection();

$id = $_GET['id'];
$client_fields = json_decode(file_get_contents("php://input"), true);

verifyHttpBodyJSON($client_fields);

if (!in_array($client_fields['gender'], ['male', 'female'])) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'gender field must be \'male\' or \'female\'']));
}

$client_fields['phone'] = phone_format($client_fields['phone']);
$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
$phoneNumberObject = $phoneNumberUtil->parse($client_fields['phone'], 'RU');

if (!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'Phone is not valid']));
}


$client = new Clients($db);

$client_update_result = $client->update($id, $client_fields);

if($client_update_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$client_update_result}"]));
}

if($client_update_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$client_update_result}"]));
}

echo json_encode(['success' => true]);