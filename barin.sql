-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.4:3306
-- Время создания: Апр 17 2026 г., 23:34
-- Версия сервера: 8.4.6
-- Версия PHP: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `barin`
--

-- --------------------------------------------------------

--
-- Структура таблицы `callback_requests`
--

CREATE TABLE `callback_requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `comment` text,
  `status` varchar(50) DEFAULT 'Новый',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `callback_requests`
--

INSERT INTO `callback_requests` (`id`, `user_id`, `name`, `phone`, `comment`, `status`, `notes`, `created_at`) VALUES
(5, 9, 'Carlsson767', '89536113232', '123123', 'Новый', '', '2026-04-10 13:29:53'),
(6, 15, 'qwer123', '89536113232', '', 'в работе', '', '2026-04-10 15:59:22');

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `filter_id` varchar(50) DEFAULT NULL,
  `sort_order` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `filter_id`, `sort_order`) VALUES
(1, 'Закуски', 'appetizer', 'app', 1),
(2, 'Основные блюда', 'main-course', 'main', 2),
(3, 'Горячее', 'goryachee', 'dessert', 3),
(5, 'Напитки', 'napitki', NULL, 0),
(6, 'тестовая категория', 'testovaya-kategoriya', NULL, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock` int UNSIGNED NOT NULL DEFAULT '10',
  `category_id` int NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `sort_order` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `stock`, `category_id`, `image_path`, `sort_order`) VALUES
(1, 'Чесночный хлеб с сыром', 'Поджаренный багет с миксом из моцареллы и ароматного чесночного масла.', 700.00, 10, 1, 'garlic-bread.jpg', 1),
(2, 'Куриные крылышки BBQ', 'Крылышки гриль в дымной глазури барбекю, посыпанные кунжутом.', 1100.00, 10, 2, 'bbq-wings.jpg', 1),
(3, 'Фаршированные грибы', 'Запеченные грибы, фаршированные расплавленным сыром, жареным луком и травами.', 500.00, 10, 1, 'stuffed-mushrooms.jpg', 2),
(4, 'Тыквенный крем-суп', 'Нежный бархатистый суп из спелой тыквы со свежими сливками и специями.', 900.00, 10, 1, 'pumpkin-soup.jpg', 3),
(5, 'Брускетта Помодоро', 'Хрустящий хлеб со свежими томатами, чесноком, оливковым маслом и базиликом.', 800.00, 9, 1, 'bruschetta.jpg', 4),
(6, 'Хрустящий Цезарь', 'Свежий салат ромэн с чесночными гренками и стружкой пармезана.', 600.00, 10, 1, 'caesar.jpg', 5),
(7, 'Филе лосося на гриле', 'Обжаренный лосось, подается на картофельном пюре с лимонно-сливочным соусом.', 1700.00, 8, 2, 'salmon.jpg', 2),
(8, 'Курица с травами', 'Насладитесь нашей томленой курицей, идеально замаринованной с  чесноком.', 1501.00, 101, 3, 'herb-chicken.jpg', 3),
(9, 'товар тест', 'описание для товара тестовое ', 1900.00, 9, 6, '69e13d11e10e1-5e0445d31d61f4aa4b7e9d87b08957b5.jpg', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `menu_settings`
--

CREATE TABLE `menu_settings` (
  `id` int NOT NULL,
  `hero_image` varchar(255) NOT NULL,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_subtitle` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `menu_settings`
--

INSERT INTO `menu_settings` (`id`, `hero_image`, `hero_title`, `hero_subtitle`) VALUES
(1, 'hero-bg.jpg', 'Откройте для себя нашу изысканную кухню', 'От неизменной классики до авторских шедевров — наше меню воспевает свежие ингредиенты и высокое кулинарное мастерство.');

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id_news` int NOT NULL,
  `name_News` varchar(255) NOT NULL,
  `short_News` text NOT NULL,
  `long_News` text NOT NULL,
  `pic_news` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id_news`, `name_News`, `short_News`, `long_News`, `pic_news`) VALUES
(1, 'В нашем ресторане с 16-22 февраля действует Масленичное меню', 'Масленичное меню', 'В нашем ресторане с 16-22 февраля действует Масленичное меню\r\n\r\nИ мы участвуем в городском фестивале «БлинФест»', 'mals.jpg'),
(2, 'Дичь, комфорт и впечатления!', 'Гастро-ужин в «Барине»!', '6 позиций-историй, рассказанных шефами @chef_pavlov @himmlisches_gemuse\r\n\r\nВедущая: Любовь Сигорских\r\nКавер-группа «Online band», фото/видео съемка и сюрпризы от нашего ресторана.\r\n\r\nСтоимость: 6000₽\r\nБронирование: пишите в what’s app\r\n+7 980 769-07-49\r\nЗвоните +7 920 519-45-45 Артём', 'fedy.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `customer_address` text NOT NULL,
  `customer_comment` text,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Новый',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_phone`, `customer_address`, `customer_comment`, `total_price`, `status`, `created_at`) VALUES
(2, 15, 'qwer123', '89536113232', 'орел строевская 1', '', 2500.00, 'Подтвержден', '2026-04-14 23:37:45'),
(3, 15, 'qwer123', '89536113232', '1', '', 1900.00, 'Новый', '2026-04-17 15:02:54');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `quantity`, `price`) VALUES
(2, 2, 'Брускетта Помодоро', 1, 800.00),
(3, 2, 'Филе лосося на гриле', 1, 1700.00),
(4, 3, 'товар тест', 1, 1900.00);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `must_change_password` tinyint(1) DEFAULT '0',
  `role` varchar(50) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `phone`, `address`, `email`, `reset_token`, `must_change_password`, `role`) VALUES
(9, 'Carlsson767', 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, 'ivan.bazhenov.07@inbox.ru', '873b75e40c102022f4cd8a80ef8c91f8f7726981d9ccf18e7e8fac2c3bc5ca3d384fdffe592af4995371a5c573301e40081a', 0, 'admin'),
(15, 'qwer123', 'e10adc3949ba59abbe56e057f20f883e', '89536113232', '1', 'tg.fg.19@mail.ru', '33c0ff3e489e8e5cf1ab9bd3fa242a5b4660848197df7e24ba68949ea3cbf70bcb38f7300a4685af980f2e2a02c92e3bc3b3', 0, 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int NOT NULL,
  `user_login` varchar(255) NOT NULL,
  `menu_item_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `user_favorites`
--

INSERT INTO `user_favorites` (`id`, `user_login`, `menu_item_id`) VALUES
(3, 'qwer123', 5),
(4, 'qwer123', 7),
(6, 'qwer123', 9);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `callback_requests`
--
ALTER TABLE `callback_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Индексы таблицы `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `menu_settings`
--
ALTER TABLE `menu_settings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id_news`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Индексы таблицы `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product_unique` (`user_login`,`menu_item_id`),
  ADD KEY `user_login` (`user_login`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `callback_requests`
--
ALTER TABLE `callback_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `menu_settings`
--
ALTER TABLE `menu_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id_news` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `callback_requests`
--
ALTER TABLE `callback_requests`
  ADD CONSTRAINT `callback_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
