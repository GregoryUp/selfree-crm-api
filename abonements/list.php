<?php
require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/abonements.php';

$db = new DataBase();
$db = $db->getConnection();

$abonement = new Abonements($db);

try{
    $rows = $abonement->getList();
    header('Content-Type: application/json');
    echo json_encode($rows);
} catch(PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}