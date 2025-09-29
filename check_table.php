<?php
require_once 'php/models/Database.php';

try {
    $db = DBModel::getConnection();
    $stmt = $db->query('DESCRIBE personnels');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Personnel table columns:\n";
    foreach($columns as $col) {
        echo $col['Field'] . ' - ' . $col['Type'] . "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
