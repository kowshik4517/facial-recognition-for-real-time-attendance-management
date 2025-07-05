<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './phpmailer/src/Exception.php';
require './phpmailer/src/PHPMailer.php';
require './phpmailer/src/SMTP.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$subject = "Hello";
$body = "This is a test mode email.";
$recipientEmail = "23341A05G0@gmrit.edu.in"; // Fixed recipient

// Initialize PHPMailer
$mail = new PHPMailer(true);
try {
    // Enable SMTP Debugging (for testing purposes)
    $mail->SMTPDebug = 2; // Set to 0 in production

    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp-mail.outlook.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'stepcone@gmrit.edu.in';
    $mail->Password = 'Project@2025';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Set sender and recipient
    $mail->setFrom('stepcone@gmrit.edu.in', 'StepCone Team');
    $mail->addAddress($recipientEmail);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = nl2br(htmlspecialchars($body));

    // Send Email
    if ($mail->send()) {
        echo json_encode(["status" => "success", "message" => "Email sent successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Email could not be sent."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
?>
