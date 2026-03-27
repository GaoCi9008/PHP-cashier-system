<?php
require 'config.php';

if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit;
}

$products = $pdo->query("SELECT name, price FROM products ORDER BY name")->fetchAll();

$message = '';
$message_class = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 验证 CSRF 令牌
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = '非法请求';
        $message_class = 'error';
    } else {
        // 只有验证通过才处理订单
        $product_name = trim($_POST['product_name'] ?? '');
        $quantity = intval($_POST['quantity'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $paid = floatval($_POST['paid'] ?? 0);
        $total = $quantity * $price;
        $change = $paid - $total;

        if ($quantity <= 0 || $price <= 0 || empty($product_name)) {
            $message = '请正确填写商品名、数量、单价';
            $message_class = 'error';
        } elseif ($paid < $total) {
            $message = '实收金额不能小于应收金额';
            $message_class = 'error';
        } else {
            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("INSERT INTO orders (staff_id, total_amount, paid_amount, change_amount) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['staff_id'], $total, $paid, $change]);
                $order_id = $pdo->lastInsertId();

                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$order_id, $product_name, $quantity, $price, $total]);

                $pdo->commit();

                $message = '订单保存成功！找补：' . number_format($change, 2) . '元';
                $message_class = 'success';
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = '系统错误，订单保存失败';
                $message_class = 'error';
            }
        }
    }
}

include 'cashier_view.php';
