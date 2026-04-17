<?php
require_once 'core_z4.php';

// Если пользователь уже авторизован, перенаправляем на закрытую страницу
if (check_user()) {
    header("Location: menu.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход и Регистрация - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
    <script src="validation.js"></script>
    <style>
        .auth-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: var(--bg-light);
        }
        .auth-container {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 400px;
        }
        .auth-form { display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px; }
        .auth-form input { padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: var(--font-sans); }
        .auth-form button { padding: 12px; background: var(--primary-gold); color: #000; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-family: var(--font-sans); }
        .auth-form button:hover { background: #e0b13b; }
        .msg { padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; text-align: center; }
        .error { background: #ffdddd; color: #d8000c; }
        .success { background: #ddffdd; color: #4F8A10; }
        h2 { font-family: var(--font-serif); margin-bottom: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <?php if (!empty($auth_error)): ?>
                <div class="msg error"><?php echo htmlspecialchars($auth_error); ?></div>
            <?php endif; ?>
            <?php if (!empty($auth_success)): ?>
                <div class="msg success"><?php echo htmlspecialchars($auth_success); ?></div>
            <?php endif; ?>

            <h2>Вход</h2>
            <form class="auth-form" method="POST" action="login.php">
                <input type="text" name="login" placeholder="Логин" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit" name="auth">Войти</button>
                <a href="z2_reset.php" style="font-size: 14px; text-align: center; display: block; margin-top: -5px; color: var(--primary-gold);">Забыли пароль?</a>
            </form>

            <h2>Регистрация</h2>
            <form class="auth-form" name="registrationForm" method="POST" action="login.php" onsubmit="return formValidation();">
                <input type="text" name="login" placeholder="Придумайте логин">
                <input type="password" name="password" placeholder="Придумайте пароль">
                <input type="email" name="email" placeholder="Email (для восстановления пароля)">
                <input type="tel" name="phone" placeholder="Контактный телефон (обязательно)">
                <input type="text" name="address" placeholder="Адрес доставки">
                <button type="submit" name="register">Зарегистрироваться</button>
            </form>
        </div>
    </div>
</body>
</html>