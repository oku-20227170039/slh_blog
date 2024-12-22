<?php
session_start();
include 'db_connection.php';

// Kullanıcı giriş kontrolü ve admin yetkisi kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kullanıcının role değerini kontrol et
$user_id = $_SESSION['user_id'];
$query = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    // Kullanıcı bulunamazsa oturumu sonlandır ve giriş sayfasına yönlendir
    session_destroy();
    header("Location: login.php");
    exit;
}

$user = $result->fetch_assoc();
if ($user['role'] !== 'admin') {
    // Admin değilse ana sayfaya yönlendir
    header("Location: index.php");
    exit;
}

// Arama sorgusu
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Kullanıcıları arama ve listeleme
$query = "SELECT id, username, email, role FROM users";
if (!empty($search)) {
    $query .= " WHERE username LIKE ? OR email LIKE ?";
}
$stmt = $conn->prepare($query);

if (!empty($search)) {
    $like_search = '%' . $search . '%';
    $stmt->bind_param("ss", $like_search, $like_search);
}

$stmt->execute();
$result = $stmt->get_result();

// Kullanıcı silme işlemi
if (isset($_POST['delete_user'])) {
    $delete_user_id = intval($_POST['user_id']);
    if ($delete_user_id !== $_SESSION['user_id']) { // Kendi hesabını silmesin
        $delete_query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $delete_user_id);
        $stmt->execute();
        header("Location: root_panel.php");
        exit;
    }
}

// Rol değiştirme işlemi
if (isset($_POST['change_role'])) {
    $change_role_user_id = intval($_POST['user_id']);
    $new_role = $_POST['new_role'] === 'admin' ? 'admin' : 'user';
    $update_query = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $new_role, $change_role_user_id);
    $stmt->execute();
    header("Location: root_panel.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Root Masası</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: rgb(0, 0, 0);
            color: rgb(0, 128, 0);
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
        }

        .search-box {
            margin-bottom: 20px;
        }

        .search-box input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid rgb(0, 128, 0);
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: rgb(0, 128, 0);
            color: white;
        }

        .action-btn {
            background-color: rgb(255, 0, 0);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .action-btn:hover {
            background-color: rgb(200, 0, 0);
        }

        .role-select {
            padding: 5px;
            font-size: 14px;
            border: 1px solid rgb(0, 128, 0);
            border-radius: 5px;
            background-color: rgba(0, 128, 0, 0.1);
            color: rgb(0, 128, 0);
        }

        .role-select option {
            color: black;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Root Masası</h1>
        <div class="links">
            <a href="index.php">Ana Sayfa</a>
            <a href="logout.php">Çıkış Yap</a>
        </div>
    </div>

    <div class="content">
        <h2>Kullanıcı Yönetimi</h2>

        <!-- Arama Kutusu -->
        <div class="search-box">
            <form action="root_panel.php" method="GET">
                <input type="text" name="search" placeholder="Kullanıcı adı veya e-posta ara..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kullanıcı Adı</th>
                    <th>E-posta</th>
                    <th>Rol</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id']; ?></td>
                        <td>
                            <a href="user_profile.php?id=<?= $user['id']; ?>" style="text-decoration: none; color: rgb(0, 128, 0); font-weight: bold;">
                                <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <form action="root_panel.php" method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                <select name="new_role" class="role-select" onchange="this.form.submit()">
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : ''; ?>>Kullanıcı</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <input type="hidden" name="change_role" value="1">
                            </form>
                        </td>
                        <td>
                            <form action="root_panel.php" method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                <button type="submit" name="delete_user" class="action-btn" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
