<?php
require_once "db_connect.php";

$subject_id = $_GET["subject_id"] ?? null;
$section_id = $_GET["section_id"] ?? null;

if (!$subject_id || !$section_id) {
    die("Subject ID and Section ID are required.");
}

$query = "
    SELECT a.jntu_no, s.name, a.date, a.time, a.status 
    FROM attendance a
    JOIN students s ON a.jntu_no = s.jntu_no
    WHERE a.subject_id = ? AND s.section_id = ?
    ORDER BY a.date DESC, a.time DESC;
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $subject_id, $section_id);
$stmt->execute();
$result = $stmt->get_result();

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=attendance_report.csv");

$output = fopen("php://output", "w");
fputcsv($output, ["JNTU No", "Name", "Date", "Time", "Status"]);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}
fclose($output);
?>
