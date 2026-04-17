<?php
require_once 'core_z4.php';
track_visit('История посещений', $_SERVER['REQUEST_URI']);
$user = check_user();
$active_page = 'history';

if (!$user) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История посещений - Ресторан Барин</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .history-container { max-width: 800px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .history-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .history-table th, .history-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .history-table th { background-color: #f4f4f4; font-weight: bold; }
        .history-table tr:nth-child(even) { background-color: #f9f9f9; }
        .history-table tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>
    <header class="news-header" style="height: auto; min-height: 200px; padding-bottom: 20px;">
        <?php include 'navbar.php'; ?>
        <div class="hero-content small-hero">
            <h1>История посещений</h1>
        </div>
    </header>

    <section class="history-section">
        <div class="history-container">
            <h2>Ваши последние действия на сайте</h2>
            <table class="history-table">
                <tr>
                    <th>Название страницы</th>
                    <th>URL</th>
                    <th>Время посещения</th>
                </tr>
                <?php if (!empty($_SESSION['history'])): ?>
                    <?php foreach ($_SESSION['history'] as $visit): ?>
                        <tr>
                            <td><?= htmlspecialchars($visit['name']) ?></td>
                            <td><?= htmlspecialchars($visit['url']) ?></td>
                            <td><?= htmlspecialchars($visit['time']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center;">История пуста.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>