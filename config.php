<?php
// config.php

// 会话安全配置（必须在 session_start() 之前设置）
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    // 开发环境暂时不强制 HTTPS
    ini_set('session.cookie_secure', 0);
    session_start();
}

$host = '127.0.0.1';
$dbname = 'job_db';
$username = 'root';
$password = '123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>