<?php
session_start();

// Настройки подключения к базе данных
define('DB_HOST', 'MySQL-8.4'); 
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'barin');

// Подключение к базе данных
$db = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die("Ошибка подключения к БД. Проверьте запущен ли MySQL и создана ли база данных '" . DB_NAME . "'.<br>Текст ошибки: " . $db->connect_error);
}
$db->set_charset("utf8mb4");

// Функция проверки авторизации пользователя
function check_user() {
    return isset($_SESSION['current_user']) ? $_SESSION['current_user'] : '';
}

// Функция проверки уникальности имени пользователя
function check_login_unique($db, $new_login) {
    $result = $db->query("SELECT login FROM users");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (mb_strtolower($row['login']) === mb_strtolower($new_login)) {
                return false; // Найдено совпадение, логин не уникален
            }
        }
    }
    return true; // Уникальный логин
}

// Отслеживание истории посещений
function track_visit($page_name, $url) {
    if (!isset($_SESSION['history'])) {
        $_SESSION['history'] = [];
    }
    // Записываем, если страница отличается от предыдущей
    $last = end($_SESSION['history']);
    if (!$last || $last['url'] !== $url) {
        $_SESSION['history'][] = [
            'name' => $page_name,
            'url' => $url,
            'time' => date('H:i:s')
        ];
    }
}

// Обработка выхода из аккаунта
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Обработка форм авторизации и регистрации
$auth_error = '';
$auth_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login']) && isset($_POST['password'])) {
        $login = $db->real_escape_string($_POST['login']);
        $password = md5($_POST['password']); // Хэширование пароля
        
        if (isset($_POST['register'])) {
            if (!check_login_unique($db, $login)) {
                $auth_error = 'Такой логин уже существует. Пожалуйста, придумайте новый логин.';
            } else {
                $phone = isset($_POST['phone']) ? $db->real_escape_string($_POST['phone']) : '';
                $address = isset($_POST['address']) ? $db->real_escape_string($_POST['address']) : '';
                $email = isset($_POST['email']) ? $db->real_escape_string($_POST['email']) : '';
                
                // Добавляем явное указание роли 'user' при регистрации
                if ($db->query("INSERT INTO users (login, password, phone, address, email, role) VALUES ('$login', '$password', '$phone', '$address', '$email', 'user')")) {
                    $auth_success = 'Регистрация успешна! Теперь вы можете войти.';
                } else {
                    $auth_error = 'Ошибка при регистрации: ' . $db->error;
                }
            }
        } elseif (isset($_POST['auth'])) {
            $res = $db->query("SELECT * FROM users WHERE login='$login' AND password='$password'");
            if ($res === false) {
                $auth_error = 'Ошибка БД. Возможно, в базе не создана таблица `users`.';
            } elseif ($res->num_rows > 0) {
                $user_data = $res->fetch_assoc();
                $_SESSION['current_user'] = $login;
                $_SESSION['user_role'] = $user_data['role'];
                $_SESSION['user_id'] = $user_data['id']; // Сохраняем ID пользователя в сессию
                
                // Проверка на временный пароль для принудительной смены
                if (isset($user_data['must_change_password']) && $user_data['must_change_password'] == 1) {
                    header("Location: z2_change_pass.php");
                    exit;
                } else {
                    header("Location: menu.php");
                    exit;
                }
            } else {
                $auth_error = 'Неверный логин или пароль.';
            }
        }
    }
}
// === E-COMMERCE FUNCTIONS (from lab 12-13) ===
/**
 * Получает список товаров из базы данных.
 * @param mysqli $db - объект подключения к БД
 * @param int|null $category_id - ID категории для фильтрации
 * @return array - массив товаров
 */
function getProducts($db, $category_id = null) {
    $sql = "SELECT mi.*, c.name AS category_name FROM menu_items mi LEFT JOIN categories c ON mi.category_id = c.id ORDER BY mi.id DESC";
    $params = [];
    $types = '';

    if ($category_id) {
        $sql = "SELECT mi.*, c.name AS category_name FROM menu_items mi LEFT JOIN categories c ON mi.category_id = c.id WHERE mi.category_id = ? ORDER BY mi.id DESC";
        $params[] = $category_id;
        $types .= 'i';
    }

    $stmt = $db->prepare($sql);
    if ($category_id) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
    return $products;
}

/**
 * Получает массив ID избранных товаров для пользователя.
 * @param mysqli $db - объект подключения к БД
 * @param int $user_id - ID пользователя
 * @return array - массив ID товаров
 */
function get_user_favorite_ids($db, $user_login) {
    if (empty($user_login)) return [];
    $sql = "SELECT menu_item_id FROM user_favorites WHERE user_login = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $user_login);
    $stmt->execute();
    $result = $stmt->get_result();
    $favorite_ids = array_column($result->fetch_all(MYSQLI_ASSOC), 'menu_item_id');
    $stmt->close();
    return $favorite_ids;
}

function getProductsByIds($db, $ids) {
    if (empty($ids)) return [];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT mi.*, c.name AS category_name FROM menu_items mi LEFT JOIN categories c ON mi.category_id = c.id WHERE mi.id IN ($placeholders)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[$row['id']] = $row;
    }
    $stmt->close();
    return $products;
}