<?php
include_once '../config/database.php';
include_once '../objects/client.php';

$db = new Database();
$db = $db->getConnection();

$clients = new Clients($db);

try {
    $count = $clients->getCountOfClients();
    echo json_encode(["count" => $count]);
} catch (PDOException $e) {
    echo 'ERROR';
}
