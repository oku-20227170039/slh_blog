<?php
session_start();
include 'db_connection.php';

// Kullanıcının giriş yapıp yapmadığını kontrol et
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kullanıcı bilgilerini al
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "Kullanıcı bilgileri alınamadı.";
    exit;
}

// Kullanıcı bilgilerini güncelleme işlemi
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');

    if (!empty($username) && !empty($email)) {
        $update_query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $username, $email, $user_id);

        if ($stmt->execute()) {
            $success = "Bilgileriniz başarıyla güncellendi.";
            $_SESSION['username'] = $username; // Oturum bilgilerini de güncelle
        } else {
            $error = "Bilgiler güncellenirken bir hata oluştu.";
        }
    } else {
        $error = "Tüm alanları doldurunuz.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
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
            max-width: 400px;
            margin: 50px auto;
            background-color: rgba(0, 128, 0, 0.1);
            padding: 20px;
            border-radius: 5px;
            color: rgb(0, 128, 0);
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 15px;
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
            background-color: rgb(0, 128, 0);
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .form button:hover {
            background-color: rgb(0, 84, 0);
        }

        .error-message, .success-message {
            font-weight: bold;
            margin-top: 15px;
            text-align: center;
        }

        .error-message {
            color: rgb(255, 0, 0);
        }

        .success-message {
            color: rgb(0, 255, 0);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SLH_BLOG</h1>
        <div class="links">
            <a href="index.php">Ana Sayfa</a>
            <a href="logout.php">Çıkış Yap</a>
        </div>
    </div>

    <div class="content">
        <h2>Profil Bilgileriniz</h2>

        <?php if (!empty($error)): ?>
            <p class="error-message"><?= $error; ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success-message"><?= $success; ?></p>
        <?php endif; ?>

        <form action="profile.php" method="POST" class="form">
            <label for="username">Kullanıcı Adı:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="email">E-posta:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label>Rol:</label>
            <p><?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></p>

            <button type="submit">Güncelle</button>
        </form>
    </div>
</body>
</html>
