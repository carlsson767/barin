<?php
require_once 'core_z4.php';

if (!check_user()) {
    header("Location: login.php");
    exit;
}

$login = $_SESSION['current_user'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $new_password = md5($_POST['new_password']); // Хэшируем новый пароль
    
    // Сохраняем новый пароль и убираем флаг
    if ($db->query("UPDATE users SET password='$new_password', must_change_password=0 WHERE login='$login'")) {
        $msg = "<div class='msg success'>Пароль успешно изменен! Вы будете перенаправлены в меню...</div>";
        header("refresh:2;url=menu.php");
    } else {
        $msg = "<div class='msg error'>Ошибка при обновлении пароля.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Смена пароля - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-wrapper { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: var(--bg-light); }
        .auth-container { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; }
        .auth-form { display: flex; flex-direction: column; gap: 15px; margin-top: 20px; }
        .auth-form input { padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: var(--font-sans); }
        .auth-form button { padding: 12px; background: var(--primary-gold); border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-family: var(--font-sans); }
        .msg { padding: 15px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; }
        .error { background: #ffdddd; color: #d8000c; }
        .success { background: #ddffdd; color: #4F8A10; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <h2>Смена временного пароля</h2>
            <?= $msg ?>
            <p style="font-size: 14px; color: #666; margin-bottom: 15px;">Пожалуйста, установите новый постоянный пароль для вашей учетной записи.</p>
            <form class="auth-form" method="POST">
                <input type="password" name="new_password" placeholder="Новый пароль" required minlength="4">
                <button type="submit">Сохранить пароль</button>
            </form>
        </div>
    </div>
</body>
</html>