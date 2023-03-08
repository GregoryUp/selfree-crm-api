<?php

require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/abonements.php';
require_once '../objects/tariffs.php';

$db = new DataBase();
$db = $db->getConnection();

$id = $_GET['id'];

$abonement_fields = json_decode(file_get_contents("php://input"), true);

if ($abonement_fields === null) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'INVALID_JSON']));
}

if (empty($abonement_fields)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

$abonement_fields['tariff_id'] = intval($abonement_fields['tariff_id']);
$abonement_fields['price'] = doubleval($abonement_fields['price']);

$tariff = new Tariffs($db);
$tariff_getList_result = $tariff->getList();

if ($tariff_getList_result == 'QUERY_FAILED') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$tariff_getList_result}"]));
}

$tariff_ids = array_column($tariff_getList_result, 'id');

if(!in_array($abonement_fields['tariff_id'], $tariff_ids)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'NOT_FOUND_TARIFF']));
}

$abonement = new Abonements($db);

$abonement_update_result = $abonement->update($id, $abonement_fields);

if($abonement_update_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$abonement_update_result}"]));
}

if($abonement_update_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$abonement_update_result}"]));
}

echo 'OK';