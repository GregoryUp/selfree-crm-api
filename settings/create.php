<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/settings.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new Database();
$db = $db->getConnection();

$table_name = $_GET['object'];
$setting_fields = json_decode(file_get_contents("php://input"), true);

verifyHttpBodyJSON($setting_fields);


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

$setting_create_result = $setting->create($setting_fields);

if($setting_create_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$setting_create_result}"]));
}

if($setting_create_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$setting_create_result}"]));
}

echo json_encode(['success' => true]);