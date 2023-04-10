<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/tariffs.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];

$tariff_fields = json_decode(file_get_contents("php://input"), true);

verifyHttpBodyJSON($tariff_fields);

$tariff_fields['duration'] = intval($tariff_fields['duration']);
$tariff_fields['price'] = doubleval($tariff_fields['price']);

$tariff = new Tariffs($db);
$tariff_update_result = $tariff->update($id, $tariff_fields);

if ($tariff_update_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$tariff_update_result}"]));
}

if ($tariff_update_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$tariff_update_result}"]));
}

echo json_encode(['success' => true]);
