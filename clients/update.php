<?php

include_once '../config/database.php';
include_once '../objects/client.php';

$db = new Database();
$db = $db->getConnection();

$id = intval($_GET['id']) != 0 ? intval($_GET['id']) : die("ERROR_PARAMETER");

$data = [];

$data['surname'] = $_POST['surname'];
$data['name'] = $_POST['name'];
$data['middlename'] = $_POST['middlename'];
$data['phone'] = $_POST['phone'];
$data['gender'] = $_POST['gender'];
$data['date_birth'] = $_POST['date_birth'];

$client = new Clients($db);

try {
    $client->update($id, $data);
    echo 'OK';
} catch (PDOException $e) {
    print_r($e);
    echo 'ERROR_REQUEST';
}
