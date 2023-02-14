<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/abonements.php';
include_once '../objects/tariffs.php';

$db = new Database();
$db = $db->getConnection();

if (empty($_POST)) exit(json_encode(['status' => "Data is empty"]));

$name = $_POST['name'];
$tariff_id = intval($_POST['tariff_id']);
$price = doubleval($_POST['price']);

$tariff = new Tariffs($db);
$tariff = $tariff->getList();

$tariff_ids = array_column($tariff, 'id');

if(!in_array($tariff_id, $tariff_ids)) {
    http_response_code(400);
    exit(json_encode(['schema' => 'Such tariff doesn\'t exist']));
}

$abonement = new Abonements($db);

try {
    $abonement->create($name, $tariff_id, $price);
    echo 'OK';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}