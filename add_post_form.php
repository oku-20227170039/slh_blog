<?php
session_start(); // Oturumu başlat

// Kullanıcının giriş yapıp yapmadığını kontrol et
if (!isset($_SESSION['user_id'])) {
    echo '<p style="color: red;">Bu sayfaya erişebilmek için giriş yapmanız gerekiyor.</p>';
    exit; // Sayfanın geri kalanını çalıştırmayı durdur
}

// Düzenleme işlemleri buradan devam eder...
?>
<form action="add_post.php" method="POST">
    <label for="title">Başlık:</label><br>
    <input type="text" name="title" id="title" required><br><br>
    
    <label for="content">İçerik:</label><br>
    <textarea name="content" id="content" rows="10" required></textarea><br><br>
    
    <button type="submit">Kaydet</button>
</form>
