<?php
require_once 'core_z4.php';
track_visit('Корзина', $_SERVER['REQUEST_URI']);

$cart_items = $_SESSION['cart'] ?? [];
$products_in_cart = [];
$subtotal = 0;

if (!empty($cart_items)) {
    $product_ids = array_keys($cart_items);
    $products_data = getProductsByIds($db, $product_ids);

    foreach ($cart_items as $id => $quantity) {
        if (isset($products_data[$id])) {
            $product = $products_data[$id];
            $products_in_cart[] = [
                'id' => $id,
                'name' => $product['name'],
                'description' => $product['description'],
                'image' => $product['image_path'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'stock' => $product['stock']
            ];
            $subtotal += $product['price'] * $quantity;
        } else {
            unset($_SESSION['cart'][$id]);
        }
    }
}

$active_page = 'cart';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="z1_style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
</head>
<body>

    <header class="page-header">
        <?php include 'navbar.php'; ?>
    </header>

    <main class="cart-page-container">
        <div class="shopping-cart">
            <div class="title">
                Корзина
            </div>

            <?php if (!empty($products_in_cart)): ?>
                <?php foreach ($products_in_cart as $item): ?>
                <div class="item" data-product-id="<?= $item['id'] ?>">
                    <div class="buttons">
                        <span class="delete-btn"></span>
                        <span class="like-btn"></span>
                    </div>

                    <div class="image">
                        <img src="images/<?= htmlspecialchars($item['image'] ?: 'default.jpg') ?>" alt="<?= htmlspecialchars($item['name']) ?>" />
                    </div>

                    <div class="description">
                        <span><?= htmlspecialchars($item['name']) ?></span>
                    </div>

                    <div class="quantity">
                        <button class="plus-btn" type="button" name="button">
                            +
                        </button>
                        <input type="text" name="name" value="<?= $item['quantity'] ?>" data-stock="<?= $item['stock'] ?>">
                        <button class="minus-btn" type="button" name="button">
                            −
                        </button>
                    </div>

                    <div class="total-price" data-price-per-item="<?= $item['price'] ?>">
                        <?= $item['price'] * $item['quantity'] ?> руб.
                    </div>
                </div>
                <?php endforeach; ?>
                 <div class="cart-footer">
                    <div class="total-sum">
                        Итого: <span id="cart-subtotal"><?= $subtotal ?></span> руб.
                    </div>
                    <div class="cart-actions">
                        <button class="btn-clear-cart">Очистить корзину</button>
                        <a href="z5_checkout.php" class="btn-checkout">Оформить заказ</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="item">
                    <p style="width: 100%; text-align: center; padding: 40px 0;">Ваша корзина пуста.</p>
                </div>
                 <div class="cart-footer" style="justify-content: center;">
                    <a href="menu.php" class="btn-checkout">Перейти в меню</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="z1_script.js"></script>
</body>
</html>