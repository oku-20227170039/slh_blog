<?php
session_start();
include 'db_connection.php';

// Kullanıcının admin olup olmadığını kontrol et
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Kategori ID'sini kontrol et
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $category_id = intval($_GET['id']);

    // Veritabanından kategori bilgilerini getir
    $query = "SELECT * FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        echo "Kategori bulunamadı!";
        exit;
    }
} else {
    header("Location: categories.php");
    exit;
}

// Form gönderildiyse güncelleme işlemini yap
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = htmlspecialchars(trim($_POST['category_name']), ENT_QUOTES, 'UTF-8');

    if (!empty($category_name)) {
        $update_query = "UPDATE categories SET name = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $category_name, $category_id);

        if ($update_stmt->execute()) {
            header("Location: categories.php");
            exit;
        } else {
            $error = "Bir hata oluştu, lütfen tekrar deneyin.";
        }
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
    <title>Kategoriyi Düzenle</title>
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
        <h2 class="baslik";>Kategoriyi Düzenle</h2>

        <!-- Hata Mesajı -->
        <?php if (isset($error)): ?>
            <p class="message error"><?= $error; ?></p>
        <?php endif; ?>

        <!-- Düzenleme Formu -->
        <form action="edit_category.php?id=<?= $category_id; ?>" method="POST" class="form">
            <label class="altbaslik"; for="category_name">Kategori Adı:</label>
            <input type="text" id="category_name" name="category_name" value="<?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <button type="submit">Güncelle</button>
        </form>
    </div>
</body>
</html>
