<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/tariffs.php';

$db = new Database();
$db = $db->getConnection();

if (empty($_POST)) exit(json_encode(['status' => "Data is empty"]));

$name = $_POST['name'];
$duration = intval($_POST['duration']);
$price = doubleval($_POST['price']);

$tariff = new Tariffs($db);

try {
    $tariff->create($name, $duration, $price);
    echo 'OK';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}