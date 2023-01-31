<?php
include_once '../config/database.php';
include_once '../objects/teachers.php';

$db = new Database();
$db = $db->getConnection();

$teachers = new Teachers($db);

try {
    $count = $teachers->getCountOfTeachers();
    header('Content-Type: application/json');
    echo json_encode(["count" => $count]);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}
