<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db_connect.php';

// ✅ Read JSON Data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Invalid input data"]);
    exit;
}

$jntu_no = $data['jntu_no'];
$name = $data['name'];
$year = $data['year'];
$semester = $data['semester'];
$branch_id = $data['branch_id'];
$section_id = $data['section_id'];
$profile_photo = $data['profile_photo']; // Base64 image

// ✅ Insert Student Data
$query = "INSERT INTO students (jntu_no, name, year, semester, branch_id, section_id, profile_photo) 
          VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssiiiss", $jntu_no, $name, $year, $semester, $branch_id, $section_id, $profile_photo);

if ($stmt->execute()) {
    echo json_encode(["message" => "Student registered successfully!"]);
} else {
    echo json_encode(["error" => "Failed to register student"]);
}
?>
