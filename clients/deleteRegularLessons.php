<?php
require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];

$client = new Clients($db);

$client_deleteRegularLessons_result = $client->deleteRegularLessons($id);

if($client_deleteRegularLessons_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$client_deleteRegularLessons_result}"]));
}

if($client_deleteRegularLessons_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$client_deleteRegularLessons_result}"]));
}

echo json_encode(['success' => true]);