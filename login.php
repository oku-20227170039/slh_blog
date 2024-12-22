<?php
session_start();
include 'db_connection.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_email = htmlspecialchars(trim($_POST['username_or_email']), ENT_QUOTES, 'UTF-8');
    $password = trim($_POST['password']);

    if (!empty($username_or_email) && !empty($password)) {
        // Kullanıcı adı veya e-posta kontrolü
        $query = "SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Şifre kontrolü
            if (password_verify($password, $user['password'])) {
                // Giriş başarılı
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Yönlendirme
                header("Location: index.php");
                exit;
            } else {
                $error = "Hatalı şifre.";
            }
        } else {
            $error = "Kullanıcı adı veya e-posta bulunamadı.";
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
    <title>Giriş Yap</title>
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

        .error-message {
            color: rgb(255, 0, 0);
            font-weight: bold;
        }
        .register-button {
            display: block;
            margin: 20px auto 0 auto; /* Ortalamak için otomatik marj */
            width: 100%; /* Giriş yap butonuyla aynı genişlik */
            padding: 10px;
            font-size: 16px;
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
            background-color: rgb(0, 128, 0);
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        .register-button:hover {
            background-color: rgb(0, 84, 0);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SLH_BLOG</h1>
        <div class="links">
            <a href="index.php">Ana Sayfa</a>
            <a href="register.php">Kayıt Ol</a>
        </div>
    </div>

    <div class="content">
        <h2>Giriş Yap</h2>

        <?php if (!empty($error)): ?>
            <p class="error-message"><?= $error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST" class="form">
            <input type="text" name="username_or_email" placeholder="Kullanıcı adı veya e-posta" required>
            <input type="password" name="password" placeholder="Şifre" required>
            <button type="submit">Giriş Yap</button>
        </form>
        
        <button class="register-button" onclick="window.location.href='register.php';">Hesabın Yok Mu? Kayıt Ol</button>

    </div>
</body>
</html>
