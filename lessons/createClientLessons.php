<?php
define('DEBUG', true);

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/lessons.php';

$db = new Database();
$db = $db->getConnection();

$client_id = $_POST['client_id'];

header('Content-Type: application/json');

$lesson = new Lessons($db);

$lesson_createClientTimetable_result = $lesson->createClientTimetable($client_id);

echo json_encode($lesson_createClientTimetable_result);
