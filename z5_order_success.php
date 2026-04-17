<?php
require_once 'core_z4.php';

if (!check_user()) {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ успешно оформлен - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="page-header">
        <?php include 'navbar.php'; ?>
    </header>

    <main style="text-align: center; padding: 80px 20px;">
        <div style="max-width: 600px; margin: 0 auto;">
            <h1 style="font-family: var(--font-serif); font-size: 36px; color: #2ecc71; margin-bottom: 20px;">Спасибо за ваш заказ!</h1>
            <?php if ($order_id > 0): ?>
                <p style="font-size: 18px; color: #333;">Ваш заказ №<?= $order_id ?> успешно оформлен и передан в обработку.</p>
            <?php else: ?>
                <p style="font-size: 18px; color: #333;">Ваш заказ успешно оформлен и передан в обработку.</p>
            <?php endif; ?>
            <p style="font-size: 16px; color: #666; margin-top: 10px;">Наш менеджер свяжется с вами в ближайшее время для подтверждения деталей.</p>
            <div style="margin-top: 40px;">
                <a href="menu.php" class="btn-checkout" style="margin-right: 15px;">Вернуться в меню</a>
                <a href="profile.php" class="btn-clear-cart" style="text-decoration: none;">Посмотреть историю заказов</a>
            </div>
        </div>
    </main>
</body>
</html>