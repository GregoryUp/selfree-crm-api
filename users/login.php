<?php
require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/users.php';


$login = $_POST['login'];
$password = $_POST['password'];

$db = new Database();
$db = $db->getConnection();

$user = new Users($db);
$auth_status = $user->auth($login, $password);

if($auth_status == 'AUTH_TRUE') {
    http_response_code(200);
    exit(json_encode(['success' => true, 'message' => "{$auth_status}"]));
}

if($auth_status == 'AUTH_FALSE') {
    http_response_code(200);
    exit(json_encode(['success' => true, 'message' => "{$auth_status}"]));
}

if($auth_status == 'QUERY_FAILED') {
    http_response_code(500);
    exit(json_encode(['error' => true, 'message' => "{$auth_status}"]));
}