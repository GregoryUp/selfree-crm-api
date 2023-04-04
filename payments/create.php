<?php
require_once '../config/database.php';

require_once '../objects/payments.php';

$db = new Database();
$db = $db->getConnection();

if (empty($_POST)) {
    http_response_code(400);
    header('Content-Type: application/json');
    exit(json_encode(['error' => true, 'message' => 'Data is empty']));
}

$client_id = intval($_POST['client_id']);
$amount = doubleval($_POST['amount']);
$comment = $_POST['comment'] ?? '';
$operation_type_id = intval($_POST['operation_type_id']);

$payment = new Payments($db);

$payment->create([
    'client_id' => $client_id,
    'amount' => $amount,
    'comment' => $comment,
    'operation_type_id' => $operation_type_id
]);

echo 'OK';
