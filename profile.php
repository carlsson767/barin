<?php
require_once 'core_z4.php';
if (!check_user()) {
    header("Location: login.php");
    exit;
}
track_visit('Личный кабинет', $_SERVER['REQUEST_URI']);
$active_page = 'profile';

$login = $_SESSION['current_user'];
$res = $db->query("SELECT * FROM users WHERE login='$login'");
$user_info = $res->fetch_assoc();
$user_id = $user_info['id'];

// Получаем историю заказов пользователя
$orders_res = $db->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");

// Обработка сообщений после отправки формы обратного звонка
$callback_msg = '';
if (isset($_SESSION['callback_msg_success'])) {
    $callback_msg = "<div class='msg success'>" . htmlspecialchars($_SESSION['callback_msg_success']) . "</div>";
    unset($_SESSION['callback_msg_success']);
} elseif (isset($_SESSION['callback_msg_error'])) {
    $callback_msg = "<div class='msg error'>" . htmlspecialchars($_SESSION['callback_msg_error']) . "</div>";
    unset($_SESSION['callback_msg_error']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="z1_style.css">
    <style>
        .profile-container { max-width: 800px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; flex-wrap: wrap; gap: 30px; }
        .profile-info, .callback-form-section { flex: 1; min-width: 300px; }
        .profile-info h2, .callback-form-section h2 { font-family: var(--font-serif); margin-bottom: 20px; color: var(--text-dark); }
        .profile-info p { font-size: 16px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .profile-info strong { color: var(--text-dark); display: inline-block; width: 120px; font-weight: bold;}
        .callback-form { display: flex; flex-direction: column; gap: 15px; margin-top: 20px; }
        .callback-form input, .callback-form textarea { padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: var(--font-sans); }
        .callback-form button { padding: 12px; background: var(--primary-gold); color: #000; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-family: var(--font-sans); }
        .callback-form button:hover { background: #e0b13b; }
        .msg { padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; text-align: center; }
        .error { background: #ffdddd; color: #d8000c; }
        .success { background: #ddffdd; color: #4F8A10; }
    </style>
</head>
<body>
    <header class="news-header" style="height: auto; min-height: 200px; padding-bottom: 20px;">
        <?php include 'navbar.php'; ?>
        <div class="hero-content small-hero">
            <h1>Личный кабинет</h1>
        </div>
    </header>

    <section class="history-section">
        <div style="max-width: 1000px; margin: 0 auto;">
            <div class="profile-container">
                <div class="profile-info">
                    <h2>Ваши данные</h2>
                    <p><strong>Логин:</strong> <?= htmlspecialchars($user_info['login'] ?? 'Не указано') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user_info['email'] ?? 'Не указано') ?></p>
                    <p><strong>Телефон:</strong> <?= htmlspecialchars($user_info['phone'] ?? 'Не указано') ?></p>
                    <p><strong>Адрес:</strong> <?= htmlspecialchars($user_info['address'] ?? 'Не указано') ?></p>
                </div>

                <div class="callback-form-section">
                    <h2>Заказать обратный звонок</h2>
                    <?= $callback_msg ?>
                    <form class="callback-form" method="POST" action="z3_callback.php">
                        <input type="text" name="name" placeholder="Ваше имя" value="<?= htmlspecialchars($user_info['login'] ?? '') ?>" required>
                        <input type="tel" name="phone" placeholder="Ваш телефон" value="<?= htmlspecialchars($user_info['phone'] ?? '') ?>" required>
                        <textarea name="comment" placeholder="Комментарий (необязательно)" rows="3"></textarea>
                        <button type="submit" name="request_callback">Заказать звонок</button>
                    </form>
                </div>
            </div>

            <div class="order-history-section" style="margin-top: 40px;">
                <h2 style="font-family: var(--font-serif); text-align: center; margin-bottom: 20px;">История ваших заказов</h2>
                <?php if ($orders_res && $orders_res->num_rows > 0): ?>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>№ Заказа</th>
                                <th>Дата</th>
                                <th>Сумма</th>
                                <th>Статус</th>
                                <th>Адрес</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = $orders_res->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $order['id'] ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td><?= $order['total_price'] ?> руб.</td>
                                    <td><?= htmlspecialchars($order['status']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_address']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #666;">У вас пока нет ни одного заказа.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>