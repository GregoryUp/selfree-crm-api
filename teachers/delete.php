<?php

include_once '../config/database.php';
include_once '../objects/teachers.php';

$db = new Database();
$db = $db->getConnection();

$id = intval($_GET['id']) != 0 ? intval($_GET['id']) : die('ERROR_PARAMETER');

$teacher = new Teachers($db);

try {
    $teacher->delete($id);
    echo 'OK';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}