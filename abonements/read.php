<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/abonements.php';

$db = new DataBase();
$db = $db->getConnection();

$id = intval($_GET['id']);

$abonement = new Abonements($db);

try {
    $row = $abonement->read($id);

    if ($row == 'NOT_FOUND') {
        http_response_code(404);
        exit($row);
    }

    header('Content-Type: application/json');
    echo json_encode($row);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}