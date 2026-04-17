<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Очищаем все данные сессии
session_unset();
session_destroy();

// Удаляем cookie с историей, устанавливая время жизни в прошлом
if (isset($_COOKIE['page_history'])) {
    unset($_COOKIE['page_history']);
    setcookie('page_history', '', time() - 3600, '/');
}

// Перенаправляем на главную страницу (меню)
header("Location: menu.php");
exit;
?>