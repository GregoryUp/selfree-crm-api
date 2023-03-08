<?php

require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/abonements.php';
require_once '../objects/tariffs.php';

$db = new Database();
$db = $db->getConnection();

if (empty($_POST)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
} 

$name = $_POST['name'];
$tariff_id = intval($_POST['tariff_id']);
$duration = intval($_POST['duration']);
$price = doubleval($_POST['price']);

$tariff = new Tariffs($db);
$tariff_getList_result = $tariff->getList();

if ($tariff_getList_result == 'QUERY_FAILED') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$tariff_getList_result}"]));
}

$tariff_ids = array_column($tariff_getList_result, 'id');

if(!in_array($tariff_id, $tariff_ids)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "NOT_FOUND_TARIFF"]));
}

$abonement = new Abonements($db);

$abonement_create_result = $abonement->create([
    'name'      => $name,
    'tariff_id' => $tariff_id,
    'duration'  => $duration,
    'price'     => $price
]);

if($abonement_create_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$abonement_create_result}"]));
}

if($abonement_create_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$abonement_create_result}"]));
}

echo 'OK';