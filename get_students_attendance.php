<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// ✅ Sample JSON Data
$json_data = '{
    "subjects": [
        {
            "subject_id": 1,
            "subject_name": "Data Structures",
            "semester": 4,
            "branch_name": "CSE",
            "sections": [
                {
                    "section_id": 1,
                    "section_name": "A",
                    "students": [
                        {
                            "jntu_no": "23341A05G0",
                            "name": "M Venkata Krishna",
                            "profile_photo": "https://th.bing.com/th/id/OIP.xxYvPiXsWNEBPc13qOKzTAAAAA?rs=1&pid=ImgDetMain",
                            "attendance_percentage": 38
                        },
                        {
                            "jntu_no": "23341A05G1",
                            "name": "Ananya Reddy",
                            "profile_photo": "https://img.freepik.com/free-photo/happy-young-female-student-holding-notebooks-from-courses-smiling-camera-standing-spring-clothes-against-blue-background_1258-70161.jpg",
                            "attendance_percentage": 35
                        }
                    ]
                },
                {
                    "section_id": 2,
                    "section_name": "B",
                    "students": [
                        {
                            "jntu_no": "23341A05G5",
                            "name": "Rahul Sharma",
                            "profile_photo": "https://example.com/photos/rahul.jpg",
                            "attendance_percentage": 78
                        }
                    ]
                }
            ]
        },
        {
            "subject_id": 2,
            "subject_name": "Operating Systems",
            "semester": 4,
            "branch_name": "CSE",
            "sections": [
                {
                    "section_id": 1,
                    "section_name": "A",
                    "students": [
                        {
                            "jntu_no": "23341A06B2",
                            "name": "Priya Verma",
                            "profile_photo": "https://example.com/photos/priya.jpg",
                            "attendance_percentage": 88
                        }
                    ]
                }
            ]
        }
    ]
}';

// ✅ Decode JSON Data
$data = json_decode($json_data, true);

// ✅ Validate `subject_id` & `section_id` Parameter
if (!isset($_GET['subject_id']) || !isset($_GET['section_id'])) {
    echo json_encode(["error" => "Missing subject_id or section_id"]);
    exit;
}

$subject_id = intval($_GET['subject_id']);
$section_id = intval($_GET['section_id']);

// ✅ Find the Subject
$subject_found = null;
foreach ($data["subjects"] as $subject) {
    if ($subject["subject_id"] === $subject_id) {
        $subject_found = $subject;
        break;
    }
}

if (!$subject_found) {
    echo json_encode(["error" => "Subject not found"]);
    exit;
}

// ✅ Find the Section in the Subject
$section_found = null;
foreach ($subject_found["sections"] as $section) {
    if ($section["section_id"] === $section_id) {
        $section_found = $section;
        break;
    }
}

if (!$section_found) {
    echo json_encode(["error" => "Section not found in this subject"]);
    exit;
}

// ✅ Return JSON Response
echo json_encode([
    "subject_name" => $subject_found["subject_name"],
    "semester" => $subject_found["semester"],
    "branch_name" => $subject_found["branch_name"],
    "section_name" => $section_found["section_name"],
    "students" => $section_found["students"]
]);
?>
