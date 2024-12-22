<?php
try {
    $dsn = "mysql:host=localhost;dbname=slh_blog;charset=utf8mb4";
    $username = "root"; // XAMPP varsayılan kullanıcı adı
    $password = ""; // Varsayılan şifre boş

    // PDO ile veritabanına bağlan
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Başarı mesajını kaldırıyoruz
    // echo "Veritabanına bağlantı başarılı!";
} catch (PDOException $e) {
    echo "Veritabanı bağlantı hatası: " . $e->getMessage();
    exit; // Hata durumunda scripti sonlandır
}
?>
