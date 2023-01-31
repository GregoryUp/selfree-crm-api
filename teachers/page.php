<?php

include_once '../config/database.php';
include_once '../objects/teachers.php';

$page = intval($_GET['page']);

$db = new Database();
$db = $db->getConnection();

$teacher = new Teachers($db);

try{
    $teachers = $teacher->page($page);
    header('Content-Type: application/json');
    echo json_encode($teachers);
} catch(PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}