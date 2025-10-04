<?php
require '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    // Ensure record exists
    $stmt = $pdo->query("SELECT * FROM landing_about_us WHERE id = 1");
    $current = $stmt->fetch();

    if (!$current) {
        $pdo->prepare("INSERT INTO landing_about_us (id, title, content) VALUES (1, '', '')")->execute();
        $current = ['title' => '', 'content' => ''];
    }

    // Archive old data
    $archiveStmt = $pdo->prepare("
        INSERT INTO landing_archives (about_title, about_content)
        VALUES (:title, :description)
    ");
    $archiveStmt->execute([
        ':title'       => $current['title'],
        ':description' => $current['content']
    ]);

    // Update
    $update = $pdo->prepare("UPDATE landing_about_us SET title = :title, content = :content WHERE id = 1");
    $update->execute([':title' => $title, ':content' => $content]);

    $activeTab = $_POST['active_tab'] ?? 'about';
    $msg = 'About Us updated';
    header("Location: customizelanding.php?active_tab=" . urlencode($activeTab) . "&msg=" . urlencode($msg));
    exit;
}
