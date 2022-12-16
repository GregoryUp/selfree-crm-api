<?php

include_once '../config/database.php';
include_once '../objects/client.php';

$page = intval($_GET['page']);

$db = new Database();
$db = $db->getConnection();

$client = new Clients($db);

$clients = $client->page($page);

echo json_encode($clients);