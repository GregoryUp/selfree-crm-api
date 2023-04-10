<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/abonements.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/tariffs.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];

$abonement_fields = json_decode(file_get_contents("php://input"), true);

verifyHttpBodyJSON($abonement_fields);

$abonement_fields['tariff_id'] = intval($abonement_fields['tariff_id']);
$abonement_fields['price'] = doubleval($abonement_fields['price']);

$tariff = new Tariffs($db);
$tariff_getList_result = $tariff->getList();

if ($tariff_getList_result == 'QUERY_FAILED') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$tariff_getList_result}"]));
}

$tariff_ids = array_column($tariff_getList_result, 'id');

if(!in_array($abonement_fields['tariff_id'], $tariff_ids)) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'NOT_FOUND_TARIFF']));
}

$abonement = new Abonements($db);

$abonement_update_result = $abonement->update($id, $abonement_fields);

if($abonement_update_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$abonement_update_result}"]));
}

if($abonement_update_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$abonement_update_result}"]));
}

echo json_encode(['success' => true]);