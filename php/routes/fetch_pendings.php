<?php
require 'auth_check.php';
require '../database/db_connect.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM visitation_requests ORDER BY created_at DESC");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($requests);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
