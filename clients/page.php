<?php

include_once '../config/database.php';
include_once '../objects/clients.php';

$page = intval($_GET['page']);

$db = new Database();
$db = $db->getConnection();

$client = new Clients($db);



try{
    $clients = $client->page($page);
    header('Content-Type: application/json');
    echo json_encode($clients);
} catch(PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}