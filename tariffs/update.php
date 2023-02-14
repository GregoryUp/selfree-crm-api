<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/tariffs.php';

$db = new DataBase();
$db = $db->getConnection();

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT) ? intval($_GET['id']) : die("ERROR_PARAMETER");
$json = file_get_contents("php://input");

$data = json_decode($json, true);
if($data === null) die("INVALID_JSON");
if(empty($data)) die("EMPTY_DATA");

$data['duration'] = intval($data['duration']);
$data['price'] = doubleval($data['price']);

$tariff = new Tariffs($db);

try{

    $tariff->update($id, $data);
    echo 'OK';

} catch(PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}