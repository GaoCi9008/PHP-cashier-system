<?php
require 'config.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh">
<!-- 其余 HTML 部分不变，注意表单内已有隐藏字段 -->

<head>
    <meta charset="UTF-8">
    <title>首页注册</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <h2>用户注册</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success">注册成功！</div>
    <?php endif; ?>

    <form action="register.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div class="form-group">
            <label for="username">用户名</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($_GET['username'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="password">密码（至少6位）</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">确认密码</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit">注册</button>
    </form>
    <p>已有账号？ <a href="login.php">去登录</a></p>
</body>

</html>