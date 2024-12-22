<?php
session_start();
include 'db_connection.php';

// Kullanıcının admin olup olmadığını kontrol et
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$success = $error = "";

// Form gönderildiğinde kategori ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = htmlspecialchars(trim($_POST['category_name']), ENT_QUOTES, 'UTF-8');

    if (!empty($category_name)) {
        $query = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            $success = "Kategori başarıyla eklendi!";
        } else {
            $error = "Bir hata oluştu, lütfen tekrar deneyin.";
        }

        $stmt->close();
    } else {
        $error = "Kategori adını doldurmanız gerekiyor.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Kategori Ekle</title>
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
            max-width: 600px;
            margin: 30px auto;
            text-align: center;
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .form input, .form button {
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

        .baslik{
            border: 1px dotted rgb(0, 128, 0);
            color:rgb(0, 128, 0);
            transition: background-color 1s, box-shadow 0.5s
        }

        .baslik:hover{
            background-color: rgb(137, 195, 137);
            transition: background-color 1s, box-shadow 0.5s
        }
        .altbaslik{
            
            color:rgb(0, 128, 0);
            transition: background-color 1s, box-shadow 0.5s
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SLH_BLOG</h1>
        <div class="links">
            <a href="categories.php">Kategoriler</a>
            <a href="index.php">Ana Sayfa</a>
            <a href="logout.php">Çıkış Yap</a>
        </div>
    </div>

    <div class="content">
        <h2 class="baslik";>Yeni Kategori Ekle</h2>

        <!-- Başarılı veya Hata Mesajı -->
        <?php if ($success): ?>
            <p class="message success"><?= $success; ?></p>
        <?php elseif ($error): ?>
            <p class="message error"><?= $error; ?></p>
        <?php endif; ?>

        <!-- Kategori Ekleme Formu -->
        <form action="add_category.php" method="POST" class="form">
            <label class="altbaslik"; for="category_name">Kategori Adı:</label>
            <input type="text" id="category_name" name="category_name" placeholder="Kategori adı girin..." required>

            <button type="submit">Kategori Ekle</button>
        </form>
    </div>
</body>
</html>
