<?php

require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/abonements.php';

$db = new DataBase();
$db = $db->getConnection();

$id = intval($_GET['id']);

$abonement = new Abonements($db);

try{

    $abonement->delete($id);
    echo 'OK';

} catch(PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}