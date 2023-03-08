<?php

require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/tariffs.php';

$db = new DataBase();
$db = $db->getConnection();

$id = $_GET['id'];

$tariff_fields = json_decode(file_get_contents("php://input"), true);

if ($tariff_fields === null) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
}

if (empty($tariff_fields)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

$tariff_fields['duration'] = intval($tariff_fields['duration']);
$tariff_fields['price'] = doubleval($tariff_fields['price']);

$tariff = new Tariffs($db);
$tariff_update_result = $tariff->update($id, $tariff_fields);

if($tariff_update_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$tariff_update_result}"]));
}

if($tariff_update_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$tariff_update_result}"]));
}

echo 'OK';