<?php
require_once 'core_z4.php';

// Защита страницы
if (!check_user()) {
    header("Location: login.php");
    exit;
}

// Проверка, не пуста ли корзина
$cart_items = $_SESSION['cart'] ?? [];
if (empty($cart_items)) {
    header("Location: z1_cart.php");
    exit;
}

track_visit('Оформление заказа', $_SERVER['REQUEST_URI']);
$active_page = 'cart'; // Подсвечиваем корзину в меню

// Получаем данные пользователя для автозаполнения формы
$user_login = $_SESSION['current_user'];
$user_res = $db->query("SELECT * FROM users WHERE login='$user_login'");
$user_info = $user_res->fetch_assoc();

// Получаем товары из корзины
$product_ids = array_keys($cart_items);
$products_data = getProductsByIds($db, $product_ids);

$subtotal = 0;
foreach ($cart_items as $id => $quantity) {
    if (isset($products_data[$id])) {
        $subtotal += $products_data[$id]['price'] * $quantity;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="z1_style.css">
    <style>
        .checkout-container { display: flex; max-width: 1200px; margin: 40px auto; gap: 40px; align-items: flex-start; }
        .checkout-form, .order-summary { flex: 1; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .checkout-form h2, .order-summary h2 { font-family: var(--font-serif); margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; font-size: 14px; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; }
        .order-summary .item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .order-summary .item label { display: flex; align-items: center; width: 100%; cursor: pointer; }
        .order-summary .item:last-of-type { border-bottom: none; }
        .order-summary .total { font-size: 20px; font-weight: bold; text-align: right; margin-top: 20px; padding-top: 20px; border-top: 2px solid #333; }
        .btn-place-order { width: 100%; padding: 15px; font-size: 16px; margin-top: 10px; }
    </style>
</head>
<body>
    <header class="page-header">
        <?php include 'navbar.php'; ?>
        <div class="hero-content small-hero">
            <h1>Оформление заказа</h1>
        </div>
    </header>

    <main>
        <form action="z5_place_order.php" method="POST" class="checkout-container">
            <div class="checkout-form">
                <h2>Контактные данные</h2>
                <div class="form-group">
                    <label for="name">Ваше имя</label>
                    <input type="text" id="name" name="customer_name" value="<?= htmlspecialchars($user_info['login'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input type="tel" id="phone" name="customer_phone" value="<?= htmlspecialchars($user_info['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Адрес доставки</label>
                    <textarea id="address" name="customer_address" rows="3" required><?= htmlspecialchars($user_info['address'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="comment">Комментарий к заказу</label>
                    <textarea id="comment" name="customer_comment" rows="3" placeholder="Например, номер подъезда, этаж или особые пожелания"></textarea>
                </div>
            </div>

            <div class="order-summary">
                <h2>Ваш заказ</h2>
                <div class="form-group">
                    <label><input type="checkbox" id="select-all" checked> Выбрать все товары</label>
                </div>
                <?php foreach ($cart_items as $id => $quantity): ?>
                    <?php if (isset($products_data[$id])):
                        $product = $products_data[$id];
                        $item_total = $product['price'] * $quantity;
                    ?>
                    <div class="item">
                        <label>
                            <input type="checkbox" name="selected_items[]" value="<?= $id ?>" class="item-checkbox" data-price="<?= $item_total ?>" checked>
                            <span style="flex-grow: 1; margin-left: 10px;"><?= htmlspecialchars($product['name']) ?> (x<?= $quantity ?>)</span>
                            <strong><?= $item_total ?> руб.</strong>
                        </label>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="total">
                    Итого: <span id="total-price"><?= $subtotal ?></span> руб.
                </div>

                <?php if (isset($_SESSION['order_error'])): ?>
                    <p style="color: red; text-align: center; margin-top: 15px;"><?= $_SESSION['order_error'] ?></p>
                    <?php unset($_SESSION['order_error']); ?>
                <?php endif; ?>

                <button type="submit" class="btn-checkout btn-place-order">Подтвердить заказ</button>
            </div>
        </form>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        function updateTotalPrice() {
            let totalPrice = 0;
            $('.item-checkbox:checked').each(function() {
                totalPrice += parseFloat($(this).data('price'));
            });
            $('#total-price').text(totalPrice.toFixed(2));
        }

        $('#select-all').on('change', function() {
            $('.item-checkbox').prop('checked', $(this).prop('checked'));
            updateTotalPrice();
        });

        $('.item-checkbox').on('change', function() {
            if (!$(this).prop('checked')) {
                $('#select-all').prop('checked', false);
            } else {
                if ($('.item-checkbox:checked').length === $('.item-checkbox').length) {
                    $('#select-all').prop('checked', true);
                }
            }
            updateTotalPrice();
        });
    });
    </script>
</body>
</html>