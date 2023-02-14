<?php
require_once '../vendor/autoload.php';

include_once '../config/database.php';
include_once '../objects/leads.php';
include_once '../objects/source_list.php';
include_once '../functions/formatPhone.php';

$db = new DataBase();
$db = $db->getConnection();

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT) ? intval($_GET['id']) : die("ERROR_PARAMETER");
$json = file_get_contents("php://input");

$data = json_decode($json, true);
if($data === null) die("INVALID_JSON");
if(empty($data)) die("EMPTY_DATA");

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$data['phone'] = phone_format($data['phone']);

try {

    $phoneNumberObject = $phoneNumberUtil->parse($data['phone'], 'RU');

    if(!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
        throw new Exception();
    }

} catch (Exception $e) {
    http_response_code(400);
    exit(json_encode(['schema' => 'Phone is not valid']));
}


$sourceList = new SourceList($db);

$rows = $sourceList->getList();
$sources = array_column($rows, 'slug');

if(!in_array($data['source'], $sources)) {
    http_response_code(400);
    exit(json_encode(['schema' => 'Such source doesn\'t exist']));
}


$lead = new Leads($db);

try{

    $lead->update($id, $data);
    echo 'OK';

} catch(PDOException $e) {
    http_response_code(500);
    echo 'ERROR_REQUEST';
}