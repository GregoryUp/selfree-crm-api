<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/source_list.php';

$db = new Database();
$db = $db->getConnection();

$id = intval($_GET['id']);

$sourceList = new SourceList($db);

try {

    $sourceList->delete($id);
    echo 'OK';

} catch (PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}