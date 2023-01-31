<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/source_list.php';

$db = new Database();
$db = $db->getConnection();

if (empty($_POST)) exit(json_encode(['status' => "Data is empty"]));

$name = $_POST['name'];
$slug = $_POST['slug'];

$sourceList = new SourceList($db);

try {
    $sourceList->create($name, $slug);
    echo 'OK';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}