<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/abonements.php';

$db = new DataBase();
$db = $db->getConnection();

header('Content-Type: application/json');

$abonement = new Abonements($db);

$abonement_getList_result = $abonement->getList();

if($abonement_getList_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$abonement_getList_result}"]));
}

echo json_encode($abonement_getList_result);