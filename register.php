<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 先验证 CSRF 令牌
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $error = '非法请求';
    header('Location: index.php?error=' . urlencode($error));
    exit;
}

// 再获取数据
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// 验证输入
if (empty($username) || empty($email) || empty($password)) {
    $error = '所有字段都必须填写';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = '邮箱格式不正确';
} elseif (strlen($password) < 8) {
    $error = '密码至少需要8位';  // 加强密码长度
} elseif (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $error = '密码必须同时包含字母和数字';  // 增加强度验证
} elseif ($password !== $confirm_password) {
    $error = '两次密码输入不一致';
} else {
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = '用户名或邮箱已被注册';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $password_hash]);
            header('Location: index.php?success=1');
            exit;
        }
    } catch (PDOException $e) {
        $error = '系统错误，请稍后重试';
        // 可记录日志
    }
}

$params = [
    'error' => $error,
    'username' => $username,
    'email' => $email
];
header('Location: index.php?' . http_build_query($params));
exit;
