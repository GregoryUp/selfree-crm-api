<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/tariffs.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

if (empty($_POST)) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
} 

$name = $_POST['name'];
$duration = intval($_POST['duration']);
$price = doubleval($_POST['price']);

$tariff = new Tariffs($db);

$tariff_create_result = $tariff->create([
    'name' => $name,
    'duration' => $duration,
    'price' => $price
]);

if($tariff_create_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$tariff_create_result}"]));
}

if($tariff_create_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$tariff_create_result}"]));
}

echo json_encode(['success' => true]);