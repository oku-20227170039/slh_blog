<?php
session_start();
include 'db_connection.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Kullanıcı adı veya e-posta kontrolü
        $query = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Kullanıcı adı veya e-posta zaten kayıtlı.";
        } else {
            // Şifre hash'leme
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Yeni kullanıcı ekleme
            $insert_query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($insert_stmt->execute()) {
                $success = "Kayıt başarılı! Giriş yapabilirsiniz.";
            } else {
                $error = "Kayıt sırasında bir hata oluştu.";
            }
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
    <title>Kayıt Ol</title>
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

        .register-button, .login-button {
            display: block;
            margin: 20px auto 0 auto;
            width: 100%; /* Giriş Yap butonuyla aynı genişlikte */
            padding: 10px;
            font-size: 16px;
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
            background-color: rgb(0, 128, 0);
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .register-button:hover, .login-button:hover {
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
            <a href="login.php">Giriş Yap</a>
        </div>
    </div>

    <div class="content">
        <h2>Kayıt Ol</h2>

        <?php if (!empty($error)): ?>
            <p class="error-message"><?= $error; ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success-message"><?= $success; ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST" class="form">
            <input type="text" name="username" placeholder="Kullanıcı Adı" required>
            <input type="email" name="email" placeholder="E-posta" required>
            <input type="password" name="password" placeholder="Şifre" required>
            <button type="submit" class="register-button">Kayıt Ol</button>
        </form>

        <!-- Giriş Yap Butonu -->
        <button class="login-button" onclick="window.location.href='login.php';">Hesabın Var mı? Giriş Yap</button>
    </div>
</body>
</html>
