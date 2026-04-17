<?php
require_once 'core_z4.php';
track_visit('Новости', $_SERVER['REQUEST_URI']);

if (!check_user()) {
    header("Location: login.php");
    exit;
}

$active_page = 'news';

// Подтягиваем логику вывода новостей из БД
$show_full = false;
$full_news = null;
if (isset($_GET['id_news']) && is_numeric($_GET['id_news'])) {
    $id = intval($_GET['id_news']);
    $query = "SELECT * FROM news WHERE Id_news = $id";
    $result = $db->query($query);
    if ($result && $result->num_rows > 0) {
        $full_news = $result->fetch_assoc();
        $show_full = true;
    }
}

$news_list = [];
if (!$show_full) {
    $query_all = "SELECT Id_news, name_News, short_News FROM news ORDER BY Id_news DESC";
    $result_all = $db->query($query_all);
    if ($result_all && $result_all->num_rows > 0) {
        while ($row = $result_all->fetch_assoc()) {
            $news_list[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="news-header">
        <?php include 'navbar.php'; ?>
        <div class="hero-content small-hero">
            <h1>События и Новости</h1>
            <p>Узнавайте первыми о наших новинках и мероприятиях</p>
        </div>
    </header>

    <section class="news-container">
        <div class="news-content">
            <?php if ($show_full && $full_news): ?>
                <div class="full-news">
                    <?php if (!empty($full_news['pic_news'])): ?>
                        <img src="images/<?php echo htmlspecialchars($full_news['pic_news']); ?>" alt="<?php echo htmlspecialchars($full_news['name_News']); ?>">
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($full_news['name_News']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($full_news['long_News'])); ?></p>
                    <a href="news.php" class="back-link">← Вернуться к списку новостей</a>
                </div>
            <?php else: ?>
                <div class="news-list">
                    <?php if (count($news_list) > 0): ?>
                        <?php foreach ($news_list as $item): ?>
                            <div class="news-item">
                                <h3 class="news-title">
                                    <a href="news.php?id_news=<?php echo $item['Id_news']; ?>">
                                        <?php echo htmlspecialchars($item['name_News']); ?>
                                    </a>
                                </h3>
                                <p class="short-text">
                                    <?php echo nl2br(htmlspecialchars($item['short_News'])); ?>
                                </p>
                                <a href="news.php?id_news=<?php echo $item['Id_news']; ?>" class="read-more">Читать далее</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Новостей пока нет.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>