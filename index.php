<?php
session_start();
include 'db_connection.php'; // Veritabanı bağlantısı
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
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
            text-align: center;
        }

        .baslik{
            border: 1px dotted rgb(0, 128, 0);
            color:rgb(0, 128, 0);
            transition: background-color 1s, box-shadow 0.5s
        }

        .baslik:hover{
            background-color: rgb(137, 195, 137);
            transition: background-color 1s, box-shadow 0.5s
        }

        .post {
            border: 1px solid rgb(0, 128, 0);
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: rgba(0, 128, 0, 0);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 1s, box-shadow 0.5s;
        }

        .post:hover {
            background-color: rgb(137, 195, 137);
            box-shadow: 0px 8px 10px rgba(0, 0, 0, 0.2);
        }

        .post h2 {
            color: rgb(0, 128, 0);
            margin-bottom: 15px;
        }

        .post p {
            color: rgb(0, 84, 0);
            margin-bottom: 15px;
        }

        .post a {
            color: rgb(0, 165, 0);
            text-decoration: none;
            font-weight: bold;
        }

        .post a:hover {
            text-decoration: underline;
        }

        small {
            display: block;
            color: rgb(0, 84, 0);
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .links {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .post {
                margin: 10px auto;
                max-width: 90%;
            }
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>


    <div class="content">
        <h2 class="baslik">Son Yazılar</h2>

        <?php
        // Veritabanından son 3 yazıyı çek
        $query = "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC LIMIT 3";
        $result = $conn->query($query);

        if ($result->num_rows > 0):
            while ($post = $result->fetch_assoc()):
        ?>
            <div class="post">
                <h2><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?= mb_substr(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'), 0, 100); ?>...</p>
                <a href="view_post.php?id=<?= $post['id']; ?>">Devamını Oku</a>
                <small>Yayınlanma Tarihi: <?= $post['created_at']; ?></small>
            </div>
        <?php
            endwhile;
        else:
            echo "<p>Henüz yazı eklenmemiş.</p>";
        endif;

        $conn->close();
        ?>
    </div>

</body>

</html>
