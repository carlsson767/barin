<?php
require_once 'core_z4.php';

// Проверяем, авторизован ли пользователь
if (!check_user()) {
    header("Location: login.php");
    exit;
}

// Если форма была отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_callback'])) {
    $login = $_SESSION['current_user'];

    // Получаем ID пользователя
    $user_res = $db->query("SELECT id FROM users WHERE login='$login'");
    if ($user_res && $user_res->num_rows > 0) {
        $user_data = $user_res->fetch_assoc();
        $user_id = $user_data['id'];

        // Получаем и очищаем данные из формы
        $name = $db->real_escape_string(trim($_POST['name']));
        $phone = $db->real_escape_string(trim($_POST['phone']));
        $comment = $db->real_escape_string(trim($_POST['comment']));

        // Простая валидация
        if (empty($name) || empty($phone)) {
            $_SESSION['callback_msg_error'] = 'Пожалуйста, заполните все обязательные поля (Имя и Телефон).';
        } else {
            // Вставляем данные в таблицу callback_requests
            $insert_query = "INSERT INTO callback_requests (user_id, name, phone, comment) VALUES ('$user_id', '$name', '$phone', '$comment')";
            if ($db->query($insert_query)) {
                $_SESSION['callback_msg_success'] = 'Ваш запрос на обратный звонок успешно отправлен! Мы свяжемся с вами в ближайшее время.';
            } else {
                $_SESSION['callback_msg_error'] = 'Ошибка при отправке запроса: ' . $db->error;
            }
        }
    } else {
        $_SESSION['callback_msg_error'] = 'Ошибка: Не удалось найти данные текущего пользователя.';
    }
    header("Location: profile.php");
    exit;
}
?>