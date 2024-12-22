<?php
session_start();
include 'db_connection.php';

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Yazı ID'sini al
$post_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$post_id) {
    echo "Geçersiz yazı ID'si!";
    exit;
}

// Yazıyı ve yazarını kontrol et
$query = "SELECT author_id FROM posts WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $post = $result->fetch_assoc();

    // Yazı sahibi veya admin kontrolü
    if ($_SESSION['user_id'] === $post['author_id'] || $_SESSION['role'] === 'admin') {
        // Yazıya bağlı yorumları sil
        $delete_comments_query = "DELETE FROM comments WHERE post_id = ?";
        $delete_comments_stmt = $conn->prepare($delete_comments_query);
        $delete_comments_stmt->bind_param("i", $post_id);
        $delete_comments_stmt->execute();

        // Yazıyı sil
        $delete_post_query = "DELETE FROM posts WHERE id = ?";
        $delete_post_stmt = $conn->prepare($delete_post_query);
        $delete_post_stmt->bind_param("i", $post_id);

        if ($delete_post_stmt->execute()) {
            header("Location: list_posts.php");
            exit;
        } else {
            echo "Yazı silinirken bir hata oluştu.";
        }
    } else {
        echo "Bu yazıyı silme yetkiniz yok.";
    }
} else {
    echo "Yazı bulunamadı.";
}
?>
