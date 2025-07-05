<?php
header("Content-Type: application/json");
require_once "db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["subject_id"], $data["section_id"])) {
    echo json_encode(["error" => "Missing subject or section ID."]);
    exit;
}

$faculty_id = $data["faculty_id"];
$subject_id = $data["subject_id"];
$section_id = $data["section_id"];
$date = date("Y-m-d");
$time = date("H:i:s");

// Get list of students in the section
$query = "SELECT jntu_no FROM students WHERE section_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $section_id);
$stmt->execute();
$result = $stmt->get_result();

$attendance_inserted = false;

while ($row = $result->fetch_assoc()) {
    $jntu_no = $row["jntu_no"];

    // Check if attendance already exists
    $check_query = "SELECT attendance_id FROM attendance WHERE jntu_no = ? AND subject_id = ? AND date = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("sis", $jntu_no, $subject_id, $date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        // Insert attendance record
        $insert_query = "INSERT INTO attendance (jntu_no, subject_id, faculty_id, date, time, status) VALUES (?, ?, ?, ?, ?, 'Present')";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("siiss", $jntu_no, $subject_id, $faculty_id, $date, $time);
        $insert_stmt->execute();
        $attendance_inserted = true;
    }
}

echo json_encode(["success" => $attendance_inserted ? "Attendance marked successfully." : "Attendance already recorded."]);
?>
