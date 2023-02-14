<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/tariffs.php';

$db = new DataBase();
$db = $db->getConnection();

$id = intval($_GET['id']);

$tariff = new Tariffs($db);

try{

    $tariff->delete($id);
    echo 'OK';

} catch(PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}