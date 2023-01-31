<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/source_list.php';

$db = new DataBase();
$db = $db->getConnection();

$sourceList = new SourceList($db);

try{
    $rows = $sourceList->getList();
    header('Content-Type: application/json');
    echo json_encode($rows);
} catch(PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}