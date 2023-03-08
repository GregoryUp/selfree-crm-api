<?php

require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/abonements.php';

$db = new DataBase();
$db = $db->getConnection();

$id = $_GET['id'];

$abonement = new Abonements($db);

$abonement_delete_result = $abonement->delete($id);

if($abonement_delete_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$abonement_delete_result}"]));
}

if($abonement_delete_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$abonement_delete_result}"]));
}

echo 'OK';
