<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Sample JSON Data (Stored Users)
$json_users = '{
    "students": [
        { "jntu_no": "23341A05G0", "password": "password123" },
        { "jntu_no": "23341A0502", "password": "student456" }
    ],
    "faculty": [
        { "faculty_id": "FAC123", "password": "faculty123" },
        { "faculty_id": "FAC456", "password": "faculty456" }
    ]
}';

// Decode the JSON into an associative array
$users = json_decode($json_users, true);

// Read Input Data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["userType"], $data["identifier"], $data["password"])) {
    echo json_encode(["error" => "All fields are required."]);
    exit;
}

$userType = $data["userType"];
$identifier = $data["identifier"];
$password = $data["password"];

// Validate User
$found_user = null;
if ($userType === "student") {
    foreach ($users["students"] as $student) {
        if ($student["jntu_no"] === $identifier && $student["password"] === $password) {
            $found_user = $student;
            break;
        }
    }
} elseif ($userType === "faculty") {
    foreach ($users["faculty"] as $faculty) {
        if ($faculty["faculty_id"] === $identifier && $faculty["password"] === $password) {
            $found_user = $faculty;
            break;
        }
    }
}

// Check Credentials
if ($found_user) {
    echo json_encode([
        "success" => true,
        "identifier" => $identifier
    ]);
} else {
    echo json_encode(["error" => "Invalid credentials."]);
}
?>
