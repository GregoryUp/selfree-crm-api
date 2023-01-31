<?php
require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/leads.php';

$db = new DataBase();
$db = $db->getConnection();

$lead = new Leads($db);

try{
    $rows = $lead->getList();
    header('Content-Type: application/json');
    echo json_encode($rows);
} catch(PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}