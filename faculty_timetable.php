<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// ✅ Sample JSON Data for Faculty and Timetable
$json_data = '{
    "faculty": [
        {
            "faculty_id": "FAC123",
            "faculty_name": "DR. G.shivaji",
            "faculty_branch": "Computer Science",
            "timetable": [
                {
                    "schedule_id": 101,
                    "day_of_week": "Monday",
                    "period_start": "09:00:00",
                    "period_end": "10:00:00",
                    "is_lab": 0,
                    "subject_id": 1,
                    "subject_name": "Data Structures",
                    "semester": 4,
                    "section_id": 1,
                    "section_name": "A",
                    "branch_name": "CSE"
                },
                {
                    "schedule_id": 102,
                    "day_of_week": "Wednesday",
                    "period_start": "10:00:00",
                    "period_end": "11:00:00",
                    "is_lab": 0,
                    "subject_id": 2,
                    "subject_name": "Operating Systems",
                    "semester": 4,
                    "section_id": 2,
                    "section_name": "B",
                    "branch_name": "CSE"
                }
            ]
        },
        {
            "faculty_id": "FAC456",
            "faculty_name": "Prof. Jane Smith",
            "faculty_branch": "Information Technology",
            "timetable": [
                {
                    "schedule_id": 201,
                    "day_of_week": "Tuesday",
                    "period_start": "11:00:00",
                    "period_end": "12:00:00",
                    "is_lab": 1,
                    "subject_id": 3,
                    "subject_name": "Database Management",
                    "semester": 4,
                    "section_id": 3,
                    "section_name": "C",
                    "branch_name": "IT"
                }
            ]
        }
    ]
}';

// ✅ Decode JSON Data
$data = json_decode($json_data, true);

// ✅ Validate `faculty_id` Parameter
if (!isset($_GET['faculty_id'])) {
    echo json_encode(["error" => "Missing faculty_id"]);
    exit;
}

$faculty_id = $_GET['faculty_id']; // ✅ Keep it as a string (e.g., FAC123)

// ✅ Find the Faculty and Timetable
$faculty_found = null;
foreach ($data["faculty"] as $faculty) {
    if ($faculty["faculty_id"] === $faculty_id) {
        $faculty_found = $faculty;
        break;
    }
}

// ✅ Return Faculty Timetable or Error
if ($faculty_found) {
    echo json_encode($faculty_found);
} else {
    echo json_encode(["error" => "Faculty not found with ID: $faculty_id"]);
}
?>
