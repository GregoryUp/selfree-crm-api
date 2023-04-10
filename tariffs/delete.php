<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/tariffs.php';

$db = new DataBase();
$db = $db->getConnection();

$id = $_GET['id'];

$tariff = new Tariffs($db);

$tariff_delete_result = $tariff->delete($id);

if($tariff_delete_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$tariff_delete_result}"]));
}

if($tariff_delete_result == 'QUERY_FAILED') {
    http_response_code(500);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => "{$tariff_delete_result}"]));
}

echo json_encode(['success' => true]);