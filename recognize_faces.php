<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// ✅ Python API URL
$python_api_url = "http://127.0.0.1:8006/recognize_faces/";

// ✅ Call Python API
$response = @file_get_contents($python_api_url);

if ($response === FALSE) {
    echo json_encode(["error" => "Failed to reach Python API"]);
    exit;
}

// ✅ Decode JSON Response
$data = json_decode($response, true);

// ✅ Validate API Response
if (!isset($data["faces"]) || !is_array($data["faces"])) {
    echo json_encode(["error" => "Invalid response from Python API"]);
    exit;
}

// ✅ Process Recognized Faces
$recognized_faces = [];
foreach ($data["faces"] as $face) {
    if (
        is_array($face) && 
        isset($face['jntu_no'], $face['name'])
    ) {
        $recognized_faces[] = [
            "jntu_no" => $face['jntu_no'],
            "name" => $face['name']
        ];
    }
}

// ✅ Return JSON Response
echo json_encode(["faces" => $recognized_faces]);
?>
