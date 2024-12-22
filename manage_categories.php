<?php
session_start();
include 'db_connection.php';

// Sadece admin kullanıcılar bu sayfayı görebilir
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo '<p style="color: red;">Bu sayfaya erişim yetkiniz yok.</p>';
    exit;
}

// Kategori ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');

    if (!empty($name)) {
        $query = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $name);

        if ($stmt->execute()) {
            echo '<p style="color: green;">Kategori başarıyla eklendi!</p>';
        } else {
            echo '<p style="color: red;">Bir hata oluştu. Lütfen tekrar deneyin.</p>';
        }

        $stmt->close();
    } else {
        echo '<p style="color: red;">Kategori adı boş bırakılamaz.</p>';
    }
}

// Kategori silme işlemi
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo '<p style="color: green;">Kategori başarıyla silindi!</p>';
    } else {
        echo '<p style="color: red;">Kategori silinemedi.</p>';
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Yönetimi</title>
</head>
<body>
    <h1>Kategori Yönetimi</h1>

    <!-- Kategori Ekleme Formu -->
    <form action="manage_categories.php" method="POST">
        <label for="name">Yeni Kategori Adı:</label>
        <input type="text" name="name" id="name" required>
        <button type="submit">Kategori Ekle</button>
    </form>
    <hr>

    <!-- Kategorileri Listele -->
    <h2>Mevcut Kategoriler</h2>
    <?php
    $query = "SELECT * FROM categories ORDER BY created_at DESC";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo '<ul>';
        while ($row = $result->fetch_assoc()) {
            echo '<li>' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . 
                 ' <a href="manage_categories.php?delete=' . $row['id'] . '" onclick="return confirm(\'Silmek istediğinize emin misiniz?\')">Sil</a></li>';
        }
        echo '</ul>';
    } else {
        echo '<p>Henüz kategori eklenmemiş.</p>';
    }

    $conn->close();
    ?>
</body>
</html>
