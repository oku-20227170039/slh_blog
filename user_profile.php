<?php
session_start();
include 'db_connection.php';

// Kullanıcı ID'si kontrolü
$user_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$user_id) {
    echo "Geçersiz kullanıcı ID'si!";
    exit;
}

// Kullanıcı bilgilerini al
$user_query = "SELECT username, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows === 1) {
    $user = $user_result->fetch_assoc();
} else {
    echo "Kullanıcı bulunamadı!";
    exit;
}

// Kullanıcının yazılarını al
$posts_query = "SELECT id, title, created_at FROM posts WHERE author_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($posts_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$posts_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>'nin Profili</title>
    <style>
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

        .content {
            max-width: 800px;
            margin: 30px auto;
            color: rgb(0, 128, 0);
        }

        .profile-info {
            margin-bottom: 30px;
            background-color: rgba(0, 128, 0, 0.1);
            padding: 20px;
            border-radius: 5px;
        }

        .profile-info p {
            margin: 10px 0;
            font-size: 16px;
        }

        .posts {
            margin-top: 20px;
        }

        .post {
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: rgba(0, 128, 0, 0.1);
            transition: background-color 0.3s, transform 0.3s;
        }

        .post:hover {
            background-color: rgb(137, 195, 137);
            transform: translateY(-5px);
        }

        .post a {
            text-decoration: none;
            color: rgb(0, 128, 0);
            font-weight: bold;
        }

        .post a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SLH_BLOG</h1>
        <div class="links">
            <a href="index.php">Ana Sayfa</a>
            <a href="categories.php">Kategoriler</a>
            <a href="list_posts.php">Tüm Yazılar</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Profil</a>
                <a href="logout.php">Çıkış Yap</a>
            <?php else: ?>
                <a href="login.php">Giriş Yap</a>
                <a href="register.php">Kayıt Ol</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <div class="profile-info">
            <h2><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>'nin Profili</h2>
            <p><strong>Kullanıcı Adı:</strong> <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>E-posta:</strong> <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Rol:</strong> <?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <div class="posts">
            <h3>Yazıları</h3>
            <?php if ($posts_result->num_rows > 0): ?>
                <?php while ($post = $posts_result->fetch_assoc()): ?>
                    <div class="post">
                        <a href="view_post.php?id=<?= $post['id']; ?>"><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></a>
                        <p><small>Yayınlanma Tarihi: <?= date("d-m-Y H:i", strtotime($post['created_at'])); ?></small></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Bu kullanıcı henüz bir yazı paylaşmamış.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
