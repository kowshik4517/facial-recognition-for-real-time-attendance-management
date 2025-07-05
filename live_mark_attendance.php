<?php
header("Content-Type: application/json");
require_once "db_connect.php"; 

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["jntu_no"], $data["subject_id"], $data["faculty_id"], $data["date"], $data["time"])) {
    echo json_encode(["error" => "Missing required fields."]);
    exit;
}

$jntu_no = $data["jntu_no"];
$subject_id = $data["subject_id"];
$faculty_id = $data["faculty_id"];
$date = $data["date"];
$time = $data["time"];

// Check if attendance already exists
$check_query = "SELECT attendance_id FROM attendance WHERE jntu_no = ? AND subject_id = ? AND date = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("sis", $jntu_no, $subject_id, $date);
$stmt->execute();
$check_result = $stmt->get_result();

if ($check_result->num_rows === 0) {
    // Insert attendance record
    $insert_query = "INSERT INTO attendance (jntu_no, subject_id, faculty_id, date, time, status) VALUES (?, ?, ?, ?, ?, 'Present')";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("siiss", $jntu_no, $subject_id, $faculty_id, $date, $time);
    $insert_stmt->execute();

    echo json_encode(["success" => "Attendance recorded."]);
} else {
    echo json_encode(["message" => "Attendance already marked."]);
}
?>
