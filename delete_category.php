<?php
session_start();
include 'db_connection.php';

// Admin kontrolü
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $category_id = intval($_GET['id']);
    $query = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        header("Location: categories.php");
        exit;
    } else {
        echo "Silme işlemi başarısız.";
    }
} else {
    header("Location: categories.php");
    exit;
}
?>
