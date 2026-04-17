<?php
require_once 'core_z4.php';

// Проверяем, что пользователь авторизован и запрос был отправлен методом POST
if (!check_user() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$selected_ids = $_POST['selected_items'] ?? [];
if (empty($selected_ids)) {
    $_SESSION['order_error'] = "Вы не выбрали ни одного товара для оформления заказа.";
    header("Location: z5_checkout.php");
    exit;
}

$cart_items = $_SESSION['cart'] ?? [];
$user_id = $_SESSION['user_id'] ?? 0;

// Проверяем, что корзина не пуста и ID пользователя известен
// Эта проверка все еще полезна, на случай если сессия истекла между страницами
if (empty($cart_items) || $user_id === 0) {
    header("Location: z1_cart.php");
    exit;
}

// Собираем и очищаем данные из формы
$name = $db->real_escape_string($_POST['customer_name']);
$phone = $db->real_escape_string($_POST['customer_phone']);
$address = $db->real_escape_string($_POST['customer_address']);
$comment = $db->real_escape_string($_POST['customer_comment']);

// Получаем актуальные данные о товарах из БД
$product_ids = $selected_ids;
$products_data = getProductsByIds($db, $product_ids);

$total_price = 0;
$order_items_data = [];

// Проверяем наличие и считаем итоговую стоимость
foreach ($selected_ids as $id) {
    $id = (int)$id;
    // Получаем количество из оригинальной корзины в сессии
    $quantity = $cart_items[$id] ?? 0;

    if ($quantity === 0) continue; // Пропускаем, если по какой-то причине товара нет в корзине

    if (!isset($products_data[$id]) || $products_data[$id]['stock'] < $quantity) {
        // Если товара нет или его не хватает на складе, возвращаем ошибку
        $_SESSION['order_error'] = "Ошибка: товара '" . ($products_data[$id]['name'] ?? 'ID:'.$id) . "' нет в наличии в нужном количестве.";
        header("Location: z5_checkout.php");
        exit;
    }

    $total_price += $products_data[$id]['price'] * $quantity;
    $order_items_data[] = [
        'id' => $id,
        'name' => $products_data[$id]['name'],
        'quantity' => $quantity,
        'price' => $products_data[$id]['price']
    ];
}

// Начинаем транзакцию, чтобы обеспечить целостность данных
$db->begin_transaction();

try {
    // 1. Создаем запись в таблице `orders`
    $stmt_order = $db->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, customer_comment, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_order->bind_param("issssd", $user_id, $name, $phone, $address, $comment, $total_price);
    $stmt_order->execute();
    $order_id = $db->insert_id; // Получаем ID созданного заказа

    // 2. Добавляем товары в `order_items` и обновляем остатки
    $stmt_items = $db->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt_stock = $db->prepare("UPDATE menu_items SET stock = stock - ? WHERE id = ?");

    foreach ($order_items_data as $item) {
        // Добавляем товар в заказ
        $stmt_items->bind_param("isid", $order_id, $item['name'], $item['quantity'], $item['price']);
        $stmt_items->execute();
        // Уменьшаем остаток на складе
        $stmt_stock->bind_param("ii", $item['quantity'], $item['id']);
        $stmt_stock->execute();
    }

    // Если все прошло успешно, подтверждаем транзакцию
    $db->commit();

    // 3. Удаляем из корзины только заказанные товары
    foreach ($selected_ids as $id) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: z5_order_success.php?order_id=" . $order_id);
    exit;

} catch (mysqli_sql_exception $exception) {
    $db->rollback(); // Откатываем все изменения в случае ошибки
    $_SESSION['order_error'] = "Произошла ошибка при оформлении заказа. Пожалуйста, попробуйте снова.";
    header("Location: z5_checkout.php");
    exit;
}
?>