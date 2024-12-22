<?php
session_start();
include 'db_connection.php';

// Kullanıcının admin olup olmadığını kontrol et
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Kategorileri getir
$category_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($category_query);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategoriler</title>
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

        .categories {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .category-item {
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
            padding: 15px;
            background-color: rgba(0, 128, 0, 0.1);
            color: rgb(0, 128, 0);
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s, transform 0.3s;
            min-width: 150px;
            position: relative;
        }

        .category-item a {
            text-decoration: none;
            color: rgb(0, 128, 0);
        }

        .category-item:hover {
            background-color: rgb(137, 195, 137);
            transform: translateY(-5px);
        }

        .category-item a:hover {
            text-decoration: underline;
        }

        .ekle{
            border: 1px solid rgb(128, 0, 0);
            border-radius: 5px;
            padding: 15px;
            background-color: rgb(0, 196, 0);
            color: rgb(128, 0, 0);
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s, transform 0.3s;
            min-width: 150px;
            position: relative;
            transition: 1s;
        }
        .ekle:hover{
            background-color: rgb(0, 226, 0);
            color: rgb(255, 0, 0);
            transition: 1s;
        }

        .admin-actions {
            position: absolute;
            bottom: 10px;
            bottom:-10px;
            display: flex;
            gap: 40px;
        }

        .admin-actions a {
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            color: white;
            padding: 5px 8px;
            border-radius: 5px;
        }

        .delete {
            background-color: rgb(255, 0, 0);
        }

        .delete:hover {
            background-color: rgb(200, 0, 0);
        }

        .edit {
            background-color: rgb(0, 128, 0);
        }

        .edit:hover {
            background-color: rgb(0, 84, 0);
        }
        
    </style>
</head>
<body>
    <div class="header">
        <h1>SLH_BLOG</h1>
        <div class="links">
            <a href="index.php">Ana Sayfa</a>
            <a href="add_post.php">Yeni Yazı Ekle</a>
            <a href="logout.php">Çıkış Yap</a>
        </div>
    </div>

    <div class="content">
        <h2>Kategoriler</h2>

        <!-- Admin kullanıcılar için kategori ekleme butonu -->
        <?php if ($is_admin): ?>
            <a href="add_category.php" class="ekle">Yeni Kategori Ekle</a>
        <?php endif; ?>

        <!-- Kategoriler -->
        <?php if ($categories_result->num_rows > 0): ?>
            <div class="categories">
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <div class="category-item">
                        <a href="list_posts.php?category=<?= $category['id']; ?>">
                            <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <?php if ($is_admin): ?>
                            <div class="admin-actions">
                                <a href="edit_category.php?id=<?= $category['id']; ?>" class="edit">Düzenle</a>
                                <a href="delete_category.php?id=<?= $category['id']; ?>" class="delete" onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">Sil</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="color: rgb(255, 0, 0);">Henüz kategori eklenmemiş. Lütfen bir kategori ekleyin.</p>
        <?php endif; ?>
    </div>
</body>
</html>
