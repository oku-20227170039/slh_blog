<?php
// Veritabanı bağlantı bilgileri
$servername = "autorack.proxy.rlwy.net"; // Railway bağlantı adresi (host)
$username = "root"; // Railway kullanıcı adı
$password = "gQHifJeubmZvpXHjpsyjmeUlfVyXNfJz"; // Railway şifresi
$dbname = "railway"; // Railway veritabanı adı
$port = 21459; // Railway port numarası

// Bağlantıyı oluştur
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}

// Eğer buraya kadar hata yoksa:
echo "Railway veritabanına başarıyla bağlanıldı!";
?>
