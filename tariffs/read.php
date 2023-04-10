<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/tariffs.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];

$tariff = new Tariffs($db);

$tariff_read_result = $tariff->read($id);

if($tariff_read_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$tariff_read_result}"]));
}

if($tariff_read_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$tariff_read_result}"]));
}

if ($tariff_read_result == 'NOT_FOUND') {
    http_response_code(404);
    exit(json_encode(['error' => true, 'message' => "{$tariff_read_result}"]));
}

echo json_encode($tariff_read_result);