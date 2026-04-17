<?php
$host = 'MySQL-8.4';
$username = 'root';
$password = '';
$database = 'barin';

$db = new mysqli($host, $username, $password, $database);
if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}
$db->set_charset('utf8mb4');
?>