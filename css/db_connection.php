<?php
// Veritabanı bağlantı bilgileri
$servername = "localhost"; // XAMPP kullanıyorsanız genelde localhost olur
$username = "root"; // Varsayılan kullanıcı adı
$password = ""; // Varsayılan şifre (boş bırakın)
$dbname = "slh_blog"; // Kullanılacak veritabanı adı

// Bağlantıyı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}
?>
