<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once "db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["name"], $data["email"], $data["password"], $data["branchId"], $data["selectedSubjects"], $data["selectedSection"])) {
    echo json_encode(["error" => "All fields are required."]);
    exit;
}

$name = $data["name"];
$email = $data["email"];
$password = password_hash($data["password"], PASSWORD_BCRYPT); // Secure password
$branchId = $data["branchId"];
$selectedSubjects = $data["selectedSubjects"];
$selectedSection = $data["selectedSection"];

// Insert Faculty
$query = "INSERT INTO faculty (name, branch_id, email, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("siss", $name, $branchId, $email, $password);
$success = $stmt->execute();

if ($success) {
    $faculty_id = $stmt->insert_id;

    // Assign Subjects
    foreach ($selectedSubjects as $subject_id) {
        $assign_query = "INSERT INTO faculty_subjects (faculty_id, subject_id, section_id) VALUES (?, ?, ?)";
        $assign_stmt = $conn->prepare($assign_query);
        $assign_stmt->bind_param("iii", $faculty_id, $subject_id, $selectedSection);
        $assign_stmt->execute();
    }

    echo json_encode(["success" => "Faculty registered successfully!"]);
} else {
    echo json_encode(["error" => "Failed to register faculty."]);
}
?>
