<?php
require 'config.php';

// 如果已经登录，直接跳转到收银页
if (isset($_SESSION['staff_id'])) {
    header('Location: cashier.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = '非法请求';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['staff_id'] = $user['id'];
            $_SESSION['staff_username'] = $user['username'];
            header('Location: cashier.php');
            exit;
        } else {
            $error = '用户名或密码错误';
        }
    }
}

include 'login_view.php';
