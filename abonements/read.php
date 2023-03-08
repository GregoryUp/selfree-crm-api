<?php

require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/abonements.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];

$abonement = new Abonements($db);

$abonement_read_result = $abonement->read($id);

if($abonement_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$abonement_read_result}"]));
}

if($abonement_read_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$abonement_read_result}"]));
}

if ($abonement_read_result == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => "{$abonement_read_result}"]));
}

echo json_encode($abonement_read_result);