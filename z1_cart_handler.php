<?php
require_once 'core_z4.php'; // Для доступа к сессии и БД

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$response = ['success' => false, 'message' => 'Неизвестное действие'];

if ($id > 0) {
    switch ($action) {
        case 'add':
            $product_res = $db->query("SELECT stock FROM menu_items WHERE id = $id");
            if ($product_res && $product_res->num_rows > 0) {
                $product = $product_res->fetch_assoc();
                $current_qty = $_SESSION['cart'][$id] ?? 0;

                if ($product['stock'] > $current_qty) {
                    $_SESSION['cart'][$id] = $current_qty + 1;
                    $response = ['success' => true, 'message' => 'Товар добавлен в корзину'];
                } else {
                    $response = ['success' => false, 'message' => 'Товара больше нет в наличии'];
                }
            } else {
                 $response = ['success' => false, 'message' => 'Товар не найден'];
            }
            break;

        case 'update':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
            if ($quantity > 0) {
                $product_res = $db->query("SELECT stock FROM menu_items WHERE id = $id");
                if ($product_res && $product_res->num_rows > 0) {
                    $product = $product_res->fetch_assoc();
                    if ($quantity <= $product['stock']) {
                        $_SESSION['cart'][$id] = $quantity;
                        $response = ['success' => true, 'message' => 'Количество обновлено'];
                    } else {
                        $_SESSION['cart'][$id] = $product['stock']; // Устанавливаем макс. доступное
                        $response = ['success' => false, 'message' => 'На складе недостаточно товара. Доступно: ' . $product['stock']];
                    }
                } else {
                    $response = ['success' => false, 'message' => 'Товар не найден'];
                }
            } else {
                unset($_SESSION['cart'][$id]);
                $response = ['success' => true, 'message' => 'Товар удален из корзины'];
            }
            break;

        case 'remove':
            unset($_SESSION['cart'][$id]);
            $response = ['success' => true, 'message' => 'Товар удален из корзины'];
            break;

        case 'toggle_favorite':
            $user_login = $_SESSION['current_user'] ?? '';
            if (!empty($user_login)) {
                // Проверяем, есть ли уже в избранном
                $check_sql = "SELECT id FROM user_favorites WHERE user_login = ? AND menu_item_id = ?";
                $stmt = $db->prepare($check_sql);
                $stmt->bind_param('si', $user_login, $id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Уже в избранном - удаляем
                    $delete_sql = "DELETE FROM user_favorites WHERE user_login = ? AND menu_item_id = ?";
                    $del_stmt = $db->prepare($delete_sql);
                    $del_stmt->bind_param('si', $user_login, $id);
                    $del_stmt->execute();
                    $response = ['success' => true, 'message' => 'Удалено из избранного', 'favorited' => false];
                } else {
                    // Не в избранном - добавляем
                    $insert_sql = "INSERT INTO user_favorites (user_login, menu_item_id) VALUES (?, ?)";
                    $ins_stmt = $db->prepare($insert_sql);
                    $ins_stmt->bind_param('si', $user_login, $id);
                    $ins_stmt->execute();
                    $response = ['success' => true, 'message' => 'Добавлено в избранное', 'favorited' => true];
                }
            } else {
                $response = ['success' => false, 'message' => 'Для добавления в избранное необходимо авторизоваться.'];
            }
            break;
    }
} elseif ($action === 'clear') {
    $_SESSION['cart'] = [];
    $response = ['success' => true, 'message' => 'Корзина очищена'];
}

$total_items = 0;
if (!empty($_SESSION['cart'])) {
    $total_items = array_sum($_SESSION['cart']);
}
$response['total_items'] = $total_items;

header('Content-Type: application/json');
echo json_encode($response);
exit;