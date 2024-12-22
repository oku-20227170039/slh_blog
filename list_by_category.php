<?php
include 'db_connection.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<p style="color: red;">Geçersiz kategori ID\'si.</p>';
    exit;
}

$category_id = intval($_GET['id']);

// Kategorideki yazıları çek
$query = "SELECT posts.id, posts.title, posts.created_at, users.username 
          FROM posts 
          JOIN users ON posts.author_id = users.id 
          WHERE posts.category_id = ? 
          ORDER BY posts.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<h1>Kategoriye Ait Yazılar</h1>';
    while ($post = $result->fetch_assoc()) {
        echo '<h2><a href="view_post.php?id=' . $post['id'] . '">' . htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') . '</a></h2>';
        echo '<p>Yazar: ' . htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>Tarih: ' . $post['created_at'] . '</p>';
        echo '<hr>';
    }
} else {
    echo '<p>Bu kategoride henüz yazı yok.</p>';
}

$stmt->close();
$conn->close();
?>
