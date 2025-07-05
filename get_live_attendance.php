<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// ✅ Python API URL
$python_api_url = "http://127.0.0.1:8020/recognize_faces/";

// ✅ Fetch Data from Python API
$response = @file_get_contents($python_api_url);
if ($response === FALSE) {
    echo json_encode(["error" => "Failed to reach Python API"]);
    exit;
}

// ✅ Decode JSON Response
$data = json_decode($response, true);
if (!isset($data["faces"]) || !is_array($data["faces"])) {
    echo json_encode(["error" => "Invalid response from Python API"]);
    exit;
}

// ✅ Create Live Attendance Array (Resets Every API Call)
$live_attendance = [];

foreach ($data["faces"] as $student) {
    $jntu_no = $student["jntu_no"];

    // ✅ Ignore "Unknown" Faces
    if ($jntu_no === "Unknown") {
        continue;
    }

    $live_attendance[$jntu_no] = [
        "jntu_no" => $jntu_no,
        "name" => $student["name"],
        "status" => "Present",  // ✅ Auto-mark as 
        "timestamp" => date("Y-m-d H:i:s")
    ];
}

// ✅ Return **Only Recognized Students**
echo json_encode(["faces" => array_values($live_attendance)]);
?>
