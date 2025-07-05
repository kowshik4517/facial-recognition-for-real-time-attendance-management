<?php
header("Content-Type: application/json");
require_once "db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["attendance_id"], $data["status"])) {
    echo json_encode(["error" => "Attendance ID and status are required."]);
    exit;
}

$attendance_id = $data["attendance_id"];
$status = $data["status"];

$update_query = "UPDATE attendance SET status = ? WHERE attendance_id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("si", $status, $attendance_id);
$success = $stmt->execute();

echo json_encode(["success" => $success ? "Attendance updated." : "Failed to update attendance."]);
?>
