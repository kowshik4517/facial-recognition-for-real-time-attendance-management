<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// ✅ Sample Attendance Data (JSON)
$json_data = '{
    "reports": {
        "daily": [
            { "jntu_no": "23341A05G0", "name": "John Doe", "present_days": 1, "total_days": 1, "attendance_percentage": 100 },
            { "jntu_no": "23341A05G1", "name": "Jane Smith", "present_days": 0, "total_days": 1, "attendance_percentage": 0 }
        ],
        "weekly": [
            { "jntu_no": "23341A05G0", "name": "John Doe", "present_days": 4, "total_days": 5, "attendance_percentage": 80 },
            { "jntu_no": "23341A05G1", "name": "Jane Smith", "present_days": 2, "total_days": 5, "attendance_percentage": 40 }
        ],
        "monthly": [
            { "jntu_no": "23341A05G0", "name": "John Doe", "present_days": 18, "total_days": 22, "attendance_percentage": 81.8 },
            { "jntu_no": "23341A05G1", "name": "Jane Smith", "present_days": 14, "total_days": 22, "attendance_percentage": 63.6 }
        ]
    }
}';

// ✅ Decode JSON Data
$data = json_decode($json_data, true);

// ✅ Validate Parameters
if (!isset($_GET['subject_id']) || !isset($_GET['section_id']) || !isset($_GET['filter'])) {
    echo json_encode(["error" => "Missing required parameters."]);
    exit;
}

$filter = $_GET['filter']; // daily, weekly, monthly

// ✅ Return Filtered Reports
if (isset($data["reports"][$filter])) {
    echo json_encode(["reports" => $data["reports"][$filter]]);
} else {
    echo json_encode(["error" => "Invalid filter type."]);
}
?>
