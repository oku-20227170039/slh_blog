<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="header">
    <h1>SLH_BLOG</h1>
    <form action="search.php" method="GET" style="display: inline;">
        <input type="text" name="query" placeholder="Ara..." style="padding: 5px; border-radius: 5px; border: 1px solid rgb(0, 128, 0);">
        <button type="submit" style="padding: 5px 10px; background-color: rgb(0, 128, 0); color: white; border: none; border-radius: 5px;">Ara</button>
    </form>

    <div class="links">
        <a href="index.php">Ana Sayfa</a>
        <a href="categories.php">Kategoriler</a>
        <a href="list_posts.php">Tüm Yazılar</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="add_post.php">Yeni Yazı Ekle</a>
            <a href="profile.php">Profil</a>
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="root_panel.php">Root Masası</a>
            <?php endif; ?>
            
            <a href="logout.php">Çıkış Yap</a>
        <?php else: ?>
            <a href="login.php">Giriş Yap</a>
            <a href="register.php">Kayıt Ol</a>
        <?php endif; ?>
    </div>
</div>
