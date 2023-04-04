<?php

require_once get_cfg_var('api_selfree_school_path') . '/config/database.php';
require_once get_cfg_var('api_selfree_school_path') . '/objects/lessons.php';

$db = new Database();
$db = $db->getConnection();

$client_id = $_GET['client_id'];
$date_start = $_GET['date_start'];
$date_end = $_GET['date_end'];

$lesson = new Lessons($db);

$lessons_timetable = $lesson->clientTimetable($client_id, $date_start, $date_end);

header("Content-type: application/json");

echo json_encode($lessons_timetable);