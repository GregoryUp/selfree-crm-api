<?php

include_once '../config/database.php';
include_once '../objects/client.php';

$db = new Database();
$db = $db->getConnection();

$id = intval($_GET['id']) != 0 ? intval($_GET['id']) : die("ERROR_PARAMETER");

$client = new Clients($db);

$client = $client->view($id);

echo json_encode($client);