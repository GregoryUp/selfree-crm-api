<?php
require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';

require_once get_cfg_var('api_selfree_school_path') . '/objects/clients.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/abonements.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/settings.php';

require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

$http_data = json_decode(file_get_contents("php://input"), true);

$client_id      = $_GET['client_id'];
$abonement_id   = $http_data['abonement_id'];
$subject_id     = $http_data['subject_id'];

verifyHttpBodyJSON($http_data);

$clients     = new Clients($db);
$abonements  = new Abonements($db);
$subjects    = new Settings($db, 'subjects');

$client     = $clients->read($client_id);
$abonement  = $abonements->read($abonement_id);
$subject    = $subjects->read($subject_id);

if($client == 'NOT_FOUND') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'NOT_FOUND_CLIENT']));
}

if($abonement == 'NOT_FOUND') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'NOT_FOUND_ABONEMENT']));
}

if($subject == 'NOT_FOUND') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'NOT_FOUND_SUBJECT']));
}

$client_setAbonement_result = $clients->setAbonement($client_id, $abonement_id, $subject_id);

if(gettype($client_setAbonement_result) == 'string') {
    if(strpos($client_setAbonement_result, 'ERROR_PARAMETER') !== false) {
        http_response_code(400);
        exit(json_encode(['error' => true, 'message' => "{$client_setAbonement_result}"]));
    }

    if($client_setAbonement_result == 'NOT_FOUND_ABONEMENT') {
        http_response_code(500);
        exit(json_encode(['error' => true, 'message' => "{$client_setAbonement_result}"]));
    }

    if($client_setAbonement_result == 'QUERY_FAILED') {
        http_response_code(500);
        exit(json_encode(['error' => true, 'message' => "{$client_setAbonement_result}"]));
    }
}

echo json_encode(['success' => true]);