<?php
require_once 'core_z4.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $db->real_escape_string($_POST['email']);
    $res = $db->query("SELECT * FROM users WHERE email='$email'");
    
    if ($res && $res->num_rows > 0) {

        $temp_password = substr(md5(uniqid()), 0, 8);
        $hashed_password = md5($temp_password);
        
        // Формируем токен и ссылку для сброса пароля
        $token = bin2hex(random_bytes(50));
        $db->query("UPDATE users SET reset_token='$token', password='$hashed_password', must_change_password=1 WHERE email='$email'");
        
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/z2_reset.php?token=" . $token;
        $mailText = "Сброс пароля.\nВаш временный пароль: $temp_password \nПерейдите по ссылке для сброса пароля: " . $resetLink;
        
        // Попытка отправить письмо (может не работать на локальном сервере без настройки)
        @mail($email, "Сброс пароля", $mailText);
        
        $msg = "<div class='msg success'>Временный пароль успешно сгенерирован: <b>$temp_password</b><br><br>Письмо с ссылкой-токеном отправлено на $email (симуляция).<br><br>Теперь вы можете <a href='login.php'>войти</a>, используя этот пароль.</div>";
    } else {
        $msg = "<div class='msg error'>Пользователь с таким email не найден.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Восстановление пароля - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-wrapper { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: var(--bg-light); }
        .auth-container { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; }
        .auth-form { display: flex; flex-direction: column; gap: 15px; margin-top: 20px; }
        .auth-form input { padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: var(--font-sans); }
        .auth-form button { padding: 12px; background: var(--primary-gold); border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-family: var(--font-sans); }
        .msg { padding: 15px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; text-align: left; line-height: 1.5; }
        .error { background: #ffdddd; color: #d8000c; }
        .success { background: #ddffdd; color: #4F8A10; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <h2>Восстановление пароля</h2>
            <?= $msg ?>
            <p style="font-size: 14px; color: #666;">Введите ваш Email, указанный при регистрации. Мы сгенерируем временный пароль.</p>
            <form class="auth-form" method="POST">
                <input type="email" name="email" placeholder="Ваш Email" required>
                <button type="submit">Восстановить</button>
            </form>
            <a href="login.php" style="display: block; margin-top: 20px; color: var(--primary-gold);">Вернуться ко входу</a>
        </div>
    </div>
</body>
</html>