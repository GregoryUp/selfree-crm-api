<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/leads.php';

$db = new DataBase();
$db = $db->getConnection();

$id = intval($_GET['id']);

$lead = new Leads($db);

try {
    $lead = $lead->read($id);

    if ($lead == 'NOT_FOUND') {
        http_response_code(404);
        exit($lead);
    }

    header('Content-Type: application/json');
    echo json_encode($result);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}