<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/teachers.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/settings.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/formatPhone.php';

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

if (empty($_POST)) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'EMPTY_DATA']));
}

$name = $_POST['name'];
$gender = $_POST['gender'];
$subject_ids = explode(',', $_POST['subject_ids']);
$phone = phone_format($_POST['phone']);
$email = $_POST['email'];

$phoneNumberObject = $phoneNumberUtil->parse($phone, 'RU');

if (!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'PHONE_INVALID']));
}

$teacher = new Teachers($db);
$setting = new Settings($db, 'subjects');

$subjects_list = $setting->getList();

$subject_ids = array_intersect(array_column($subjects_list, 'id'), $subject_ids);

$teacher_create_result = $teacher->create([
    'name'        => $name,
    'gender'      => $gender,
    'subject_ids' => $subject_ids,
    'phone'       => $phone,
    'email'       => $email
]);

if(gettype($teacher_create_result) == 'string') {
    if(strpos($teacher_create_result, 'ERROR_PARAMETER') !== false) {
        http_response_code(400);
        exit(json_encode(['error' => true, 'message' => "{$teacher_create_result}"]));
    }

    if($teacher_create_result == 'QUERY_FAILED') {
        http_response_code(500);
        exit(json_encode(['error' => true, 'message' => "{$teacher_create_result}"]));
    }
}

echo json_encode(['success' => true]);