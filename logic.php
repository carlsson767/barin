<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Гость';
}

// --- Логика сохранения истории посещений в Cookie ---

$history = [];
// 1. Читаем существующую историю из cookie
if (isset($_COOKIE['page_history'])) {
    // Декодируем JSON-строку обратно в массив
    $history = json_decode($_COOKIE['page_history'], true);
    if (!is_array($history)) { // Проверка на случай повреждения cookie
        $history = [];
    }
}

// 2. Определяем текущую страницу и добавляем в историю
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

// 3. Сохраняем обновленную историю обратно в cookie
// Кодируем массив в JSON-строку и устанавливаем cookie на 30 дней
setcookie('page_history', json_encode($history), time() + (86400 * 30), "/");