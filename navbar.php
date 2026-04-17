<?php
// Определяем активный пункт меню (передаётся из вызывающей страницы)
$active_page = $active_page ?? '';

// Определяем текущего пользователя (ключ current_user задается в core_z4.php)
$username = !empty($_SESSION['current_user']) ? $_SESSION['current_user'] : 'Гость';

// Получаем количество товаров в корзине
$cart_item_count = !empty($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<nav class="navbar">
    <div class="logo">Барин</div>
    <div class="nav-links">
        <a href="menu.php" <?php if ($active_page == 'menu') echo 'class="active"'; ?>>Меню</a>
        <a href="news.php" <?php if ($active_page == 'news') echo 'class="active"'; ?>>Новости</a>
        <a href="favorites.php" <?php if ($active_page == 'favorites') echo 'class="active"'; ?>>Избранное</a>
        <a href="history.php" <?php if ($active_page == 'history') echo 'class="active"'; ?>>История</a>
    </div>
    <div class="user-panel">
        <a href="z1_cart.php" class="nav-link nav-cart-link" style="color: #fff; margin-right: 15px; text-decoration: none;">
            Корзина
            <span class="cart-count" style="background: var(--primary-gold); color: #000; border-radius: 50%; padding: 2px 7px; font-size: 12px; font-weight: bold; <?= $cart_item_count > 0 ? '' : 'display: none;' ?>"><?= $cart_item_count ?></span>
        </a>
        <a href="profile.php" class="user-greeting" style="color: #fff; text-decoration: underline; font-weight: 600;">Привет, <?php echo htmlspecialchars($username); ?>!</a>
        <a href="logout.php" class="btn-logout">Выход</a>
    </div>
</nav>