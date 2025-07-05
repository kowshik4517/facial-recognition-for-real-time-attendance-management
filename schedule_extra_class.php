<?php
header("Content-Type: application/json");
require_once "db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["faculty_id"], $data["subject_id"], $data["section_id"], $data["extra_time"])) {
    echo json_encode(["error" => "Missing required parameters."]);
    exit;
}

$faculty_id = $data["faculty_id"];
$subject_id = $data["subject_id"];
$section_id = $data["section_id"];
$extra_time = $data["extra_time"];
$day_of_week = date("l"); // Current day (Monday, Tuesday, etc.)

$query = "
    INSERT INTO timetable (section_id, faculty_id, subject_id, day_of_week, period_start, period_end, is_lab)
    VALUES (?, ?, ?, ?, ?, ADDTIME(?, '01:00:00'), FALSE);
";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiisss", $section_id, $faculty_id, $subject_id, $day_of_week, $extra_time, $extra_time);
$success = $stmt->execute();

echo json_encode(["success" => $success ? "Extra class scheduled." : "Failed to schedule extra class."]);
