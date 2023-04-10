<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/tariffs.php';

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