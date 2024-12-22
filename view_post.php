<?php
session_start();
include 'db_connection.php';

// Yazı ID'sini kontrol et
$post_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$post_id) {
    echo "Geçersiz yazı ID'si!";
    exit;
}

// Yazıyı ve yazar bilgilerini al
$post_query = "
    SELECT posts.id, posts.title, posts.content, posts.created_at, posts.author_id, users.username AS author_username 
    FROM posts 
    JOIN users ON posts.author_id = users.id 
    WHERE posts.id = ?";
$stmt = $conn->prepare($post_query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post_result = $stmt->get_result();

if ($post_result->num_rows === 0) {
    echo "Yazı bulunamadı!";
    exit;
}

$post = $post_result->fetch_assoc();

// Yorumları çek
$comments_query = "
    SELECT comments.id, comments.content, comments.created_at, comments.user_id, users.username 
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE comments.post_id = ? 
    ORDER BY comments.created_at DESC";
$comment_stmt = $conn->prepare($comments_query);
$comment_stmt->bind_param("i", $post_id);
$comment_stmt->execute();
$comments = $comment_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></title>
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

        .post-meta {
            font-size: 14px;
            color: rgb(0, 84, 0);
            margin-bottom: 20px;
        }

        .post-actions {
            margin-top: 20px;
            text-align: center;
        }

        .post-actions a {
            color: rgb(255, 0, 0);
            font-size: 14px;
            text-decoration: none;
            margin-right: 10px;
        }

        .post-actions a:hover {
            text-decoration: underline;
        }

        .add-comment {
            margin-top: 40px;
            border-top: 1px solid rgb(0, 128, 0);
            padding-top: 20px;
        }

        .add-comment h3 {
            margin-bottom: 10px;
            color: rgb(0, 128, 0);
        }

        .add-comment textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
            resize: vertical;
            min-height: 100px;
        }

        .add-comment button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: rgb(0, 128, 0);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .add-comment button:hover {
            background-color: rgb(0, 84, 0);
        }

        .comments {
            margin-top: 40px;
        }

        .comment {
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: rgba(0, 128, 0, 0.1);
            position: relative;
            height: 50px;
        }

        .comment .comment-author {
            font-weight: bold;
            color: rgb(0, 128, 0);
            position: absolute;
            top: 10px;
            left: 15px;
        }

        .comment .comment-time {
            font-size: 12px;
            color: rgb(0, 84, 0);
            position: absolute;
            bottom: 10px;
            right: 15px;
        }

        .comment .comment-actions {
            position: absolute;
            top: 10px;
            right: 15px;
        }

        .comment-comment{
            position: absolute;
            top: 50%;
            left: 15px;
        }

        .comment .comment-actions a {
            color: rgb(255, 0, 0);
            text-decoration: none;
            font-size: 12px;
        }

        .comment .comment-actions a:hover {
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
        <h1><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <div class="post-meta">
            Yazar: <a href="user_profile.php?id=<?= $post['author_id']; ?>"><?= htmlspecialchars($post['author_username'], ENT_QUOTES, 'UTF-8'); ?></a><br>
            Yayınlanma Tarihi: <?= date("d-m-Y H:i", strtotime($post['created_at'])); ?>
        </div>
        <div>
            <?= nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')); ?>
        </div>

        <div class="post-actions">
            <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] === $post['author_id'] || $_SESSION['role'] === 'admin')): ?>
                <a href="delete_post.php?id=<?= $post_id; ?>" onclick="return confirm('Bu yazıyı silmek istediğinize emin misiniz?')">Yazıyı Sil</a>
            <?php endif; ?>
        </div>

        <div class="add-comment">
            <?php if (isset($_SESSION['user_id'])): ?>
                <h3>Yorum Yap</h3>
                <form action="add_comment.php" method="POST">
                    <textarea name="content" placeholder="Yorumunuzu buraya yazın..." required></textarea>
                    <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                    <button type="submit">Gönder</button>
                </form>
            <?php else: ?>
                <p>Yorum yapmak için <a href="login.php">giriş yapmanız</a> gerekiyor.</p>
            <?php endif; ?>
        </div>

        <div class="comments">
            <h3>Yorumlar</h3>
            <?php if ($comments->num_rows > 0): ?>
                <?php while ($comment = $comments->fetch_assoc()): ?>
                    <div class="comment">
                        <div class="comment-author">
                            <a href="user_profile.php?id=<?= $comment['user_id']; ?>"><?= htmlspecialchars($comment['username'], ENT_QUOTES, 'UTF-8'); ?></a>
                        </div>
                        <div class="comment-time">
                            <?= date("d-m-Y H:i", strtotime($comment['created_at'])); ?>
                        </div>
                        <div class="comment-comment";>
                            <?= nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8')); ?>
                        </div>
                        <div class="comment-actions">
                            <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] === $comment['user_id'] || $_SESSION['role'] === 'admin')): ?>
                                <a href="delete_comment.php?id=<?= $comment['id']; ?>&post_id=<?= $post_id; ?>">Sil</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Henüz yorum yapılmamış.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
