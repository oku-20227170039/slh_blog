<?php
session_start();
include 'db_connection.php';

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Yorum ID ve yazı ID'sini al
$comment_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : null;

if (!$comment_id || !$post_id) {
    echo "Geçersiz yorum veya yazı ID'si!";
    exit;
}

// Yorum sahibini kontrol et
$query = "SELECT user_id FROM comments WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $comment = $result->fetch_assoc();

    // Yorum sahibi veya admin kontrolü
    if ($_SESSION['user_id'] === $comment['user_id'] || $_SESSION['role'] === 'admin') {
        $delete_query = "DELETE FROM comments WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $comment_id);

        if ($delete_stmt->execute()) {
            header("Location: view_post.php?id=" . $post_id);
            exit;
        } else {
            echo "Yorum silinirken bir hata oluştu.";
        }
    } else {
        echo "Bu yorumu silme yetkiniz yok.";
    }
} else {
    echo "Yorum bulunamadı.";
}
?>
