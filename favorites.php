<?php
require_once 'core_z4.php';

// Защита страницы от неавторизованных пользователей
if (!check_user()) {
    header("Location: login.php");
    exit;
}

track_visit('Избранное', $_SERVER['REQUEST_URI']);
$active_page = 'favorites'; // для подсветки в навигации

// Получаем логин пользователя из сессии
$user_login = $_SESSION['current_user'] ?? '';

// Получаем ID избранных товаров
$favorite_ids = get_user_favorite_ids($db, $user_login);

// Получаем полную информацию об избранных товарах
$favorite_products = [];
if (!empty($favorite_ids)) {
    $favorite_products = getProductsByIds($db, $favorite_ids);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранное - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="z1_style.css">
</head>
<body>
    <header class="page-header">
        <?php include 'navbar.php'; ?>
        <div class="hero-content small-hero">
            <h1>Избранное</h1>
            <p>Ваши любимые блюда, отмеченные в меню</p>
        </div>
    </header>

    <section class="menu-section">
        <div class="menu-header">
            <h2>Ваш список избранного</h2>
        </div>

        <?php if (!empty($favorite_products)): ?>
            <div class="menu-grid">
                <?php foreach ($favorite_ids as $id): // Цикл по ID, чтобы сохранить порядок добавления ?>
                    <?php if (isset($favorite_products[$id])):
                        $item = $favorite_products[$id];
                    ?>
                    <div class="menu-item" data-product-id="<?php echo $item['id']; ?>">
                        <img src="images/<?php echo htmlspecialchars($item['image_path'] ?: 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <div class="item-content">
                                <span class="badge"><?php echo htmlspecialchars($item['category_name'] ?? 'Без категории'); ?></span>
                                <div class="title-row">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <span class="price"><?php echo number_format($item['price'], 0, '', ' '); ?> ₽</span>
                                </div>
                                <p class="description"><?php echo htmlspecialchars($item['description']); ?></p>
                                <p class="item-stock">На складе: <?php echo $item['stock']; ?></p>
                            </div>
                            <div class="item-footer">
                                <button class="btn-like active" data-product-id="<?php echo $item['id']; ?>" title="Удалить из избранного">
                                    <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                </button>
                                <?php if ($item['stock'] > 0): ?>
                                    <button class="btn-add-to-cart" data-product-id="<?php echo $item['id']; ?>">В корзину</button>
                                <?php else: ?>
                                    <span class="product-out-of-stock">Нет в наличии</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px 20px; background: #f9f9f9; border-radius: 8px;">
                <p style="font-size: 18px; color: #666;">В вашем списке избранного пока ничего нет.</p>
                <a href="menu.php" class="btn-checkout" style="margin-top: 20px; display: inline-block;">Перейти в меню</a>
            </div>
        <?php endif; ?>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // --- Логика добавления в корзину ---
        $('.menu-grid').on('click', '.btn-add-to-cart', function() {
            const productId = $(this).data('product-id');
            const button = $(this);
            $.post('z1_cart_handler.php', { action: 'add', id: productId }, function(response) {
                if (response.success) {
                    if (response.total_items > 0) { $('.cart-count').text(response.total_items).show(); }
                    button.text('Добавлено!');
                    setTimeout(() => button.text('В корзину'), 1500);
                } else { alert(response.message); }
            }, 'json').fail(() => alert('Ошибка при добавлении товара.'));
        });

        // --- Логика удаления из избранного ---
        $('.menu-grid').on('click', '.btn-like', function() {
            const button = $(this);
            const productId = button.data('product-id');
            const itemCard = button.closest('.menu-item');

            if (confirm('Удалить это блюдо из избранного?')) {
                $.post('z1_cart_handler.php', { action: 'toggle_favorite', id: productId }, function(response) {
                    if (response.success && response.favorited === false) {
                        itemCard.fadeOut(400, function() {
                            $(this).remove();
                            if ($('.menu-grid .menu-item').length === 0) {
                                $('.menu-section').html('<div class="menu-header"><h2>Ваш список избранного</h2></div><div style="text-align: center; padding: 50px 20px; background: #f9f9f9; border-radius: 8px;"><p style="font-size: 18px; color: #666;">В вашем списке избранного больше ничего нет.</p><a href="menu.php" class="btn-checkout" style="margin-top: 20px; display: inline-block;">Перейти в меню</a></div>');
                            }
                        });
                    } else { alert(response.message || 'Произошла ошибка.'); }
                }, 'json').fail(() => alert('Ошибка при удалении из избранного.'));
            }
        });
    });
    </script>
</body>
</html>