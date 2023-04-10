<?php
require_once get_cfg_var('api_selfree_school_path') . '/vendor/autoload.php';

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/teachers.php';
require_once get_cfg_var('api_selfree_school_path') . '/functions/formatPhone.php';

$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

$db = new Database();
$db = $db->getConnection();

header('Content-Type: application/json');

if (empty($_POST)) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'Data is empty']));
}

$name = $_POST['name'];
$gender = $_POST['gender'];
$skill = $_POST['skill'];
$email = $_POST['email'];
$phone = phone_format($_POST['phone']);

if (!in_array($gender, ['male', 'female'])) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'gender field must be \'male\' or \'female\'']));
}

$phoneNumberObject = $phoneNumberUtil->parse($phone, 'RU');

if (!($phoneNumberUtil->isValidNumberForRegion($phoneNumberObject, 'RU'))) {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => 'Phone is not valid']));
}

$teacher = new Teachers($db);

$teacher_create_result = $teacher->create([
    'name'       => $name,
    'gender'     => $gender,
    'phone'      => $phone,
    'email'      => $email,
    'skill'      => $skill
]);

if($teacher_create_result == 'ERROR_PARAMETER') {
    http_response_code(400);
    exit(json_encode(['error' => true, 'message' => "{$teacher_create_result}"]));
}

if($teacher_create_result == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$teacher_create_result}"]));
}

echo json_encode(['success' => true]);