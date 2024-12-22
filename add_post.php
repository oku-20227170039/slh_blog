<?php
session_start();
include 'db_connection.php';

// Kullanıcının giriş yapıp yapmadığını kontrol et
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Başarılı işlem bildirimi
$success = $error = "";

// Kategorileri getir
$category_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($category_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8');
    $category_id = intval($_POST['category_id']);
    $user_id = $_SESSION['user_id'];

    if (!empty($title) && !empty($content) && $category_id > 0) {
        $query = "INSERT INTO posts (title, content, category_id, author_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssii", $title, $content, $category_id, $user_id);

        if ($stmt->execute()) {
            $success = "Yazı başarıyla eklendi!";
        } else {
            $error = "Bir hata oluştu, lütfen tekrar deneyin.";
        }

        $stmt->close();
    } else {
        $error = "Tüm alanları doldurmanız ve bir kategori seçmeniz gerekiyor.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Yazı Ekle</title>
    <style>
        /* Genel Tasarım */
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
        .baslik{
            border: 1px dotted rgb(0, 128, 0);
            color:rgb(0, 128, 0);
            transition: background-color 1s, box-shadow 0.5s
        }

        .baslik:hover{
            background-color: rgb(137, 195, 137);
            transition: background-color 1s, box-shadow 0.5s
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

        .form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .form input, .form textarea, .form select, .form button {
            padding: 10px;
            font-size: 16px;
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
            background-color: rgba(0, 128, 0, 0.1);
            color: rgb(0, 128, 0);
        }

        .form button {
            cursor: pointer;
            font-weight: bold;
            background-color: rgb(0, 128, 0);
            color: white;
        }

        .form button:hover {
            background-color: rgb(0, 84, 0);
        }

        .message {
            font-weight: bold;
            margin: 20px 0;
        }

        .message.success {
            color: rgb(0, 255, 0);
        }

        .message.error {
            color: rgb(255, 0, 0);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SLH_BLOG</h1>
        <div class="links">
            <a href="index.php">Ana Sayfa</a>
            <a href="list_posts.php">Tüm Yazılar</a>
            <a href="logout.php">Çıkış Yap</a>
        </div>
    </div>

    <div class="content">
        <h2 class="baslik">Yeni Yazı Ekle</h2>

        <!-- Başarılı veya Hata Mesajı -->
        <?php if ($success): ?>
            <p class="message success"><?= $success; ?></p>
        <?php elseif ($error): ?>
            <p class="message error"><?= $error; ?></p>
        <?php endif; ?>

        <!-- Yazı Ekleme Formu -->
        <form action="add_post.php" method="POST" class="form">
            <label for="title">Başlık:</label>
            <input type="text" id="title" name="title" placeholder="Başlık girin..." required>

            <label for="content">İçerik:</label>
            <textarea id="content" name="content" rows="5" placeholder="İçeriği buraya yazın..." required></textarea>

            <label for="category">Kategori:</label>
            <select id="category" name="category_id" required>
                <option value="">Kategori Seçin</option>
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <option value="<?= $category['id']; ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Kaydet</button>
        </form>
    </div>
</body>
</html>
