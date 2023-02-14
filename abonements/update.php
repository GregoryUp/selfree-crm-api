<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/abonements.php';
include_once '../objects/tariffs.php';

$db = new DataBase();
$db = $db->getConnection();

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT) ? intval($_GET['id']) : die("ERROR_PARAMETER");
$json = file_get_contents("php://input");

$data = json_decode($json, true);
if($data === null) die("INVALID_JSON");
if(empty($data)) die("EMPTY_DATA");

$data['tariff_id'] = intval($data['tariff_id']);
$data['price'] = doubleval($data['price']);

$tariff = new Tariffs($db);
$tariff = $tariff->getList();

$tariff_ids = array_column($tariff, 'id');

if(!in_array($data['tariff_id'], $tariff_ids)) {
    http_response_code(400);
    exit(json_encode(['schema' => 'Such tariff doesn\'t exist']));
}

$abonement = new Abonements($db);

try{

    $abonement->update($id, $data);
    echo 'OK';

} catch(PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}