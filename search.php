<?php
session_start();
include 'db_connection.php';

// Arama sorgusu al
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';

$posts = [];
if (!empty($search_query)) {
    $query = "SELECT id, title, content FROM posts WHERE title LIKE ? OR content LIKE ?";
    $stmt = $conn->prepare($query);
    $like_query = '%' . $search_query . '%';
    $stmt->bind_param("ss", $like_query, $like_query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arama Sonuçları</title>
    <style>
        .post {
            border: 1px solid rgb(0, 128, 0);
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: rgba(0, 128, 0, 0.1);
            color: rgb(0, 128, 0);
        }
        .content {
            max-width: 800px;
            margin: 30px auto;
        }
        .no-results {
            color: rgb(255, 0, 0);
            font-weight: bold;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(0, 0, 0);
            color: rgb(0, 128, 0);
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
        }

        .post {
            border: 1px solid rgb(0, 128, 0);
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: rgba(0, 128, 0, 0.1);
            color: rgb(0, 128, 0);
        }

        .post h2 {
            margin: 0;
            font-size: 18px;
        }

        .post a {
            text-decoration: none;
            color: rgb(0, 165, 0);
            font-weight: bold;
        }

        .post a:hover {
            text-decoration: underline;
        }

        .no-results {
            color: rgb(255, 0, 0);
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }

        .search-box input[type="text"] {
            width: calc(100% - 50px);
            padding: 10px;
            font-size: 16px;
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
            background-color: rgba(0, 128, 0, 0.1);
            color: rgb(0, 128, 0);
        }

        .search-box button {
            padding: 10px 20px;
            background-color: rgb(0, 128, 0);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-box button:hover {
            background-color: rgb(0, 84, 0);
        }

    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="content">
        <h1>Arama Sonuçları</h1>
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <h2><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?= mb_substr(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'), 0, 100); ?>...</p>
                    <a href="view_post.php?id=<?= $post['id']; ?>">Devamını Oku</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">Sonuç bulunamadı.</p>
        <?php endif; ?>
    </div>
</body>
</html>
