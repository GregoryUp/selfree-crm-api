<?php

include_once '../config/database.php';
include_once '../objects/client.php';

$db = new Database();
$db = $db->getConnection();

if(!$db) exit(json_encode(["status" => "DB_CONN_FAILED"]));

$surname = $_POST['surname'];
$name = $_POST['name'];
$middlename = $_POST['middlename'];
$gender = $_POST['gender'];
$phone = $_POST['phone'];
$date_birth = $_POST['date_birth'];

if (!in_array($gender, ['male', 'female'])) {
    http_response_code(403);
    exit(json_encode(['schema' => 'gender field must be \'male\' or \'female\'']));
}

$client = new Clients($db);

try {
    $client->create($name, $surname, $middlename, $gender, $phone, $date_birth);
    echo 'OK';
} catch (PDOException $e) {
    echo 'ERROR';
}