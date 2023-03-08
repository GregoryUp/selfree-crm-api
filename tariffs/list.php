<?php
require_once '../vendor/autoload.php';

require_once '../config/database.php';
require_once '../objects/tariffs.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$tariff = new Tariffs($db);

$tariff_getList_result = $tariff->getList();

if($tariff_getList_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$tariff_getList_result}"]));
}

echo json_encode($tariff_getList_result);