<?php

include_once '../config/database.php';
include_once '../objects/client.php';

$db = new Database();
$db = $db->getConnection();

$id = intval($_GET['id']) != 0 ? intval($_GET['id']) : die("ERROR_PARAMETER");
$json = file_get_contents('php://input');

$data = json_decode($json, true);

if($data === null) die("INVALID_JSON");

$client = new Clients($db);

try {
    $client->update($id, $data);
    echo 'OK';
} catch (PDOException $e) {
    print_r($e);
    echo 'ERROR_REQUEST';
}
