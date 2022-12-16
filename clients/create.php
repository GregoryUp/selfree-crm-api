<?php

include_once '../config/database.php';
include_once '../objects/client.php';

$db = new Database();
$db = $db->getConnection();

$name = $_POST['name'];
$surname = $_POST['surname'];
$lastname = $_POST['lastname'];
$sex = $_POST['sex'];
$phone = $_POST['phone'];
$date_birth = $_POST['date_birth'];

$client = new Clients($db);

try {
    $client->create($name, $surname, $lastname, $sex, $phone, $date_birth);
    echo 'OK';
} catch (PDOException $e) {
    echo 'ERROR';
}