<?php
session_start();
include 'db_connection.php';

// Kullanıcı oturum bilgisi
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Yazı ID'sini al ve kontrol et
$post_id = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$post_id) {
    echo "Geçersiz yazı ID'si!";
    exit;
}

// Yazıyı al
$query = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Yazı bulunamadı!";
    exit;
}

$post = $result->fetch_assoc();

// Yetki kontrolü
if ($user_id !== $post['author_id'] && $user_role !== 'admin') {
    echo "Bu yazıyı düzenleme yetkiniz yok!";
    exit;
}

// Kategorileri getir
$category_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($category_query);

// Yazıyı güncelle
$success = $error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8');
    $category_id = intval($_POST['category_id']);

    if (!empty($title) && !empty($content) && $category_id > 0) {
        $update_query = "UPDATE posts SET title = ?, content = ?, category_id = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssii", $title, $content, $category_id, $post_id);

        if ($stmt->execute()) {
            $success = "Yazı başarıyla güncellendi!";
            // Sayfayı yenileyerek değişiklikleri göstermek için yönlendirme
            header("Location: edit_post.php?id=$post_id");
            exit;
        } else {
            $error = "Bir hata oluştu, lütfen tekrar deneyin.";
        }
    } else {
        $error = "Tüm alanları doldurmanız gerekiyor.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yazıyı Düzenle</title>
    <link rel="stylesheet" href="style.css">
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

        .form-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: rgba(0, 128, 0, 0.1);
            border-radius: 5px;
            border: 1px solid rgb(0, 128, 0);
            color: rgb(0, 128, 0);
        }

        .form-container h1 {
            margin-bottom: 20px;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-container form input,
        .form-container form textarea,
        .form-container form select,
        .form-container form button {
            padding: 10px;
            font-size: 16px;
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
            background-color: rgba(0, 128, 0, 0.1);
            color: rgb(0, 128, 0);
        }

        .form-container form button {
            background-color: rgb(0, 128, 0);
            color: white;
            cursor: pointer;
        }

        .form-container form button:hover {
            background-color: rgb(0, 84, 0);
        }

        .message {
            font-weight: bold;
            margin-bottom: 20px;
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
    <?php include 'header.php'; ?>
    <div class="form-container">
        <h1>Yazıyı Düzenle</h1>

        <!-- Başarılı/Hata Mesajları -->
        <?php if ($success): ?>
            <p class="message success"><?= $success; ?></p>
        <?php elseif ($error): ?>
            <p class="message error"><?= $error; ?></p>
        <?php endif; ?>

        <!-- Düzenleme Formu -->
        <form action="edit_post.php?id=<?= $post_id; ?>" method="POST">
            <label for="title">Başlık:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="content">İçerik:</label>
            <textarea id="content" name="content" rows="10" required><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>

            <label for="category">Kategori:</label>
            <select id="category" name="category_id" required>
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <option value="<?= $category['id']; ?>" <?= $category['id'] === $post['category_id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Kaydet</button>
        </form>
    </div>
</body>
</html>
