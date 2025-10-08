mess<?php
session_start();
require '../database/db_connect.php';
require 'audit_log.php';

// Collect form inputs
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$message = $_POST['message'] ?? null;

// Validate required fields
if (!$name || !$message) {
    echo "<script>alert('Name and message are required.'); window.history.back();</script>";
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
    // Log action
    $token = $_SESSION['user_token'] ?? null;
    log_landing_action($pdo, $token, "Submitted contact message");

    echo "<script>alert('Message sent successfully!'); window.location.href='landingpage.php';</script>";
} else {
    echo "<script>alert('Error sending message. Please try again.'); window.history.back();</script>";
}
?>
