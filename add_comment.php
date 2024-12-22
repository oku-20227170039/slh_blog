<?php
session_start();
include 'db_connection.php';

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// POST verilerini al
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
$user_id = $_SESSION['user_id'];

// Yorumun boş veya yazı ID'sinin geçersiz olup olmadığını kontrol et
if (!$post_id || empty($content)) {
    echo "Geçersiz yorum verisi!";
    exit;
}

// Yorum ekleme işlemi
$query = "INSERT INTO comments (content, post_id, user_id, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $content, $post_id, $user_id);

if ($stmt->execute()) {
    // Yorum başarılı bir şekilde eklendiğinde yazı detay sayfasına yönlendir
    header("Location: view_post.php?id=" . $post_id);
    exit;
} else {
    echo "Yorum eklenirken bir hata oluştu.";
}
?>
