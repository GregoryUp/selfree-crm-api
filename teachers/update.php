<?php
define('DEBUG', true);

require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/teachers.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/settings.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/formatPhone.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/httpBody.php';

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

$id = $_GET['id'];

$teacher_fields = json_decode(file_get_contents("php://input"), true);

verifyHttpBodyJSON($teacher_fields);

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
$teacher_fields['phone'] = phone_format($teacher_fields['phone']);
$phoneNumberObject = $phoneNumberUtil->parse($teacher_fields['phone'], 'RU');

if (!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'PHONE_INVALID']));
}

$teacher_fields['subject_ids'] = explode(',', $teacher_fields['subject_ids']);

$teacher = new Teachers($db);
$setting = new Settings($db, 'subjects');

$subjects_list = $setting->getList();

$subject_ids = array_intersect(array_column($subjects_list, 'id'), $teacher_fields['subject_ids']);

$teacher_update_result = $teacher->update($id, $teacher_fields);

if(gettype($teacher_update_result) == 'string') {
    if(strpos($teacher_update_result, 'ERROR_PARAMETER') !== false) {
        http_response_code(400);
        exit(json_encode(['error' => true, 'message' => "{$teacher_update_result}"]));
    }

    if($teacher_update_result == 'QUERY_FAILED') {
        http_response_code(500);
        exit(json_encode(['error' => true, 'message' => "{$teacher_update_result}"]));
    }
}

echo json_encode(['success' => true]);