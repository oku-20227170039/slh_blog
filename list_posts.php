<?php
session_start();
include 'db_connection.php';

// Kullanıcının oturum bilgileri
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Kategori ID'sini al ve kontrol et
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$category_name = null;

// Eğer kategori ID'si varsa, kategori adını çek
if ($category_id) {
    $category_query = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($category_query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
        $category_name = $category['name'];
    } else {
        echo "Geçersiz kategori ID'si!";
        exit;
    }
}

// Yazıları al (kategoriye göre filtrele veya tüm yazılar)
if ($category_id) {
    $query = "
        SELECT p.id, p.title, p.content, p.author_id, p.created_at, COUNT(c.id) AS comment_count
        FROM posts p
        LEFT JOIN comments c ON p.id = c.post_id
        WHERE p.category_id = ?
        GROUP BY p.id
        ORDER BY p.created_at DESC;
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
} else {
    $query = "
        SELECT p.id, p.title, p.content, p.author_id, p.created_at, COUNT(c.id) AS comment_count
        FROM posts p
        LEFT JOIN comments c ON p.id = c.post_id
        GROUP BY p.id
        ORDER BY p.created_at DESC;
    ";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $category_name ? htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8') . " Kategorisi" : "Tüm Yazılar"; ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(0, 0, 0);
            color: rgb(0, 128, 0);
        }

        .content {
            max-width: 800px;
            margin: 30px auto;
        }

        .post {
            border: 1px solid rgb(0, 128, 0);
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: rgba(0, 128, 0, 0.1);
            text-align: left;
            transition: all 0.3s ease;
        }

        .post:hover {
            background-color: rgba(137, 195, 137, 0.2);
        }

        .post h2 {
            margin: 0 0 10px;
            font-size: 18px;
            color: rgb(0, 128, 0);
        }

        .post p {
            margin: 10px 0;
            color: rgb(0, 84, 0);
        }

        .post small {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            color: rgb(0, 84, 0);
        }

        .post a {
            text-decoration: none;
            color: rgb(0, 165, 0);
            font-weight: bold;
        }

        .post a:hover {
            text-decoration: underline;
        }

        .edit-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            font-size: 14px;
            background-color: rgb(0, 128, 0);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .edit-btn:hover {
            background-color: rgb(0, 84, 0);
        }
        .back-link {
            display: block;
            margin: 20px auto;
            text-align: center;
            color: rgb(0, 165, 0);
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(0, 0, 0);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0);
            border-bottom: 2px solid rgb(0, 128, 0);
        }

        .header h1 {
            margin: 0;
            color: rgb(0, 128, 0);
        }

        .links {
            display: flex;
            gap: 15px;
        }

        .links a {
            text-decoration: none;
            color: rgb(0, 128, 0);
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .links a:hover {
            color: rgb(0, 0, 0);
            background-color: rgb(0, 128, 0);
        }
</style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="content">
        <h1>
            <?= $category_name ? htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8') . " Kategorisi" : "Tüm Yazılar"; ?>
        </h1>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($post = $result->fetch_assoc()): ?>
                <div class="post">
                    <h2><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?= mb_substr(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'), 0, 100); ?>...</p>
                    <small>Yorum Sayısı: <?= $post['comment_count']; ?></small>
                    <a href="view_post.php?id=<?= $post['id']; ?>">Devamını Oku</a>
                    
                    <!-- Yazıyı Düzenle Butonu -->
                    <?php if ($user_id === $post['author_id'] || $user_role === 'admin'): ?>
                        <a href="edit_post.php?id=<?= $post['id']; ?>" class="edit-btn">Yazıyı Düzenle</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Bu kategoride henüz yazı bulunmuyor.</p>
        <?php endif; ?>

        <a href="categories.php" class="back-link">Tüm Kategorilere Geri Dön</a>
    </div>
</body>
</html>
