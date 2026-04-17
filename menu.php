<?php
require_once 'core_z4.php';
// Защита страницы от неавторизованных пользователей
if (!check_user()) {
    header("Location: login.php");
    exit;
}
track_visit('Меню и Магазин', $_SERVER['REQUEST_URI']);
$active_page = 'menu'; // для подсветки в навигации

$user_login = $_SESSION['current_user'] ?? '';
$favorite_ids = get_user_favorite_ids($db, $user_login);
$categories_result = $db->query("SELECT id, name FROM categories ORDER BY id");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

$menu_items = getProducts($db);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Меню и Магазин - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="z1_style.css"> <!-- Подключаем стили магазина для кнопок -->
</head>
<body>
    <header class="page-header">
        <?php include 'navbar.php'; ?>
        <div class="hero-content small-hero">
            <h1>Меню и доставка</h1>
            <p>Наши лучшие блюда, которые можно заказать с доставкой</p>
        </div>
    </header>

    <section class="menu-section">
        <div class="menu-header">
            <h2>Меню на сегодня</h2>
            <div class="filter-labels">
                <label data-category-id="all" class="active">Все меню</label>
                <?php foreach ($categories as $cat): ?>
                <label data-category-id="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="menu-grid">
            <?php foreach ($menu_items as $item): ?>
            <div class="menu-item" data-category-id="<?php echo $item['category_id']; ?>">
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
                        <button class="btn-like <?php echo in_array($item['id'], $favorite_ids) ? 'active' : '' ?>" data-product-id="<?php echo $item['id']; ?>" title="Добавить в избранное">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </button>
                        <?php if ($item['stock'] > 0): ?>
                            <button class="btn-add-to-cart" data-product-id="<?php echo $item['id']; ?>">В корзину</button>
                        <?php else: ?>
                            <span class="product-out-of-stock">Нет в наличии</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // --- Логика фильтрации меню ---
        $('.filter-labels label').on('click', function() {
            // Управление активным классом
            $('.filter-labels label').removeClass('active');
            $(this).addClass('active');

            const categoryId = $(this).data('category-id');

            if (categoryId === 'all') {
                $('.menu-item').show();
            } else {
                $('.menu-item').hide();
                $('.menu-item[data-category-id="' + categoryId + '"]').show();
            }
        });

        // --- Логика добавления в корзину ---
        $('.menu-grid').on('click', '.btn-add-to-cart', function() {
            const productId = $(this).data('product-id');
            const button = $(this);

            $.post('z1_cart_handler.php', { action: 'add', id: productId }, function(response) {
                if (response.success) {
                    if (response.total_items > 0) {
                        $('.cart-count').text(response.total_items).show();
                    }
                    button.text('Добавлено!');
                    setTimeout(() => button.text('В корзину'), 1500);
                } else {
                    alert(response.message);
                }
            }, 'json').fail(() => alert('Ошибка при добавлении товара.'));
        });

        // --- Логика добавления в избранное ---
        $('.menu-grid').on('click', '.btn-like', function() {
            const button = $(this);
            const productId = button.data('product-id');

            $.post('z1_cart_handler.php', { action: 'toggle_favorite', id: productId }, function(response) {
                if (response.success) {
                    button.toggleClass('active', response.favorited);
                } else {
                    alert(response.message);
                }
            }, 'json').fail(() => alert('Ошибка при работе с избранным.'));
        });
    });
    </script>
</body>
</html>