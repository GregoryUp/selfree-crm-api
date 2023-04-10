<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/settings.php';

$db = new Database();
$db = $db->getConnection();

$table_name = $_GET['object'];

header('Content-Type: application/json');

try {
    $setting = new Settings($db, $table_name);
} catch (Exception $e) {
    if($e->getMessage() == 'SETTING_NOT_EXIST') {
        http_response_code(404);
    } else {
        http_response_code(400);
    }

    exit(json_encode(['error' => true, 'message' => "{$e->getMessage()}"]));
}

$setting_getList_result = $setting->getList();

echo json_encode($setting_getList_result);