<?php
session_start();
require '../database/db_connect.php';
require 'audit_log.php';

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Collect form inputs
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$message = $_POST['message'] ?? null;

// Validate required fields
if (!$name || !$message) {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Name and message are required.']);
    } else {
        echo "<script>alert('Name and message are required.'); window.history.back();</script>";
    }
    exit;
}

// Insert into contact_messages
$stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)");
$success = $stmt->execute([
    ':name' => $name,
    ':email' => $email,
    ':message' => $message
]);

if ($success) {
    // Send email
    $to = '5thfighterwing@mil.ph';
    $subject = 'New Contact Message from Landing Page';
    $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
    $headers = "From: noreply@yourdomain.com\r\n";

    mail($to, $subject, $body, $headers);

    // Log action
    $token = $_SESSION['user_token'] ?? null;
    log_landing_action($pdo, $token, "Submitted contact message");

    if ($isAjax) {
        echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
    } else {
        echo "<script>alert('Message sent successfully!'); window.location.href='landingpage.php';</script>";
    }
} else {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Error sending message. Please try again.']);
    } else {
        echo "<script>alert('Error sending message. Please try again.'); window.history.back();</script>";
    }
}
?>
