<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Гость';
}

$history = [];
if (isset($_COOKIE['page_history'])) {
    // Декодируем JSON-строку обратно в массив
    $history = json_decode($_COOKIE['page_history'], true);
    if (!is_array($history)) { // Проверка на случай повреждения cookie
        $history = [];
    }
}

$current_url = $_SERVER['REQUEST_URI'];
$page_name = "Неизвестная страница";
if (strpos($current_url, 'menu.php') !== false) $page_name = "Меню";
elseif (strpos($current_url, 'news.php') !== false) $page_name = "Новости";
elseif (strpos($current_url, 'history.php') !== false) $page_name = "История посещений";

// Предотвращаем добавление дубликатов при обновлении страницы
$last_entry = end($history);
if (!$last_entry || $last_entry['url'] !== $current_url) {
    $history[] = [
        'name' => $page_name,
        'url' => $current_url,
        'time' => date('d.m.Y H:i:s')
    ];
}

// Кодируем массив в JSON-строку и устанавливаем cookie на 30 дней
setcookie('page_history', json_encode($history), time() + (86400 * 30), "/");