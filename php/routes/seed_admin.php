<?php
session_start();
require '../database/db_connect.php'; // this should set up $pdo (PDO connection)

try {
    // Check if admin already exists
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'Admin'");
    $checkStmt->execute();
    $adminCount = $checkStmt->fetchColumn();

    if ($adminCount > 0) {
        header("Location: loginpage.php");
        exit;
    }

    // Unique ID for the new user
    $id = uniqid();

    // Default password
    $plainPassword = "Admin@123";
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Insert admin
    $stmt = $pdo->prepare("INSERT INTO users
      (id, full_name, email, rank, role, status, password_hash)
      VALUES (:id, :full_name, :email, :rank, :role, :status, :password_hash)");

    $stmt->execute([
        ':id' => $id,
        ':full_name' => 'System Admin',
        ':email' => 'admin@example.com',
        ':rank' => 'Captain',
        ':role' => 'Admin',
        ':status' => 'Active',
        ':password_hash' => $hash
    ]);

    $_SESSION['admin_seeded'] = true;
    header("Location: loginpage.php");
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = "Admin setup failed: " . $e->getMessage();
    header("Location: loginpage.php");
    exit;
}
