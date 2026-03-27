<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <title>收银台</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        h2 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .row {
            display: flex;
            gap: 10px;
        }

        .row .form-group {
            flex: 1;
        }

        .total-row {
            background: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            margin: 15px 0;
            font-size: 1.2em;
        }

        .total-row span {
            font-weight: bold;
            color: #007bff;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-confirm {
            background-color: #28a745;
            color: white;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
        }

        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .logout {
            text-align: right;
            margin-bottom: 10px;
        }

        .logout a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="logout">
        当前用户：<?= htmlspecialchars($_SESSION['staff_username'] ?? '') ?> | <a href="logout.php">退出收银系统请点我</a>
    </div>
    <h2>收银系统</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?= $message_class ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" id="cashierForm">
        <div class="form-group">
            <label for="product_name">商品名</label>
            <input type="text" id="product_name" name="product_name" list="product_list" required>
            <datalist id="product_list">
                <?php foreach ($products as $p): ?>
                    <option value="<?= htmlspecialchars($p['name']) ?>" data-price="<?= $p['price'] ?>">
                    <?php endforeach; ?>
            </datalist>
            <small>可选择已有商品或手动输入</small>
        </div>

        <div class="row">
            <div class="form-group">
                <label for="quantity">数量</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" required>
            </div>
            <div class="form-group">
                <label for="price">单价（元）</label>
                <input type="number" id="price" name="price" min="0.01" step="0.01" required>
            </div>
        </div>

        <div class="total-row">
            <span>应收金额：</span><span id="total_display">0.00</span> 元
            <input type="hidden" id="total" name="total" value="0">
        </div>

        <div class="form-group">
            <label for="paid">实收金额（元）</label>
            <input type="number" id="paid" name="paid" min="0" step="0.01" required>
        </div>

        <div class="total-row">
            <span>找补：</span><span id="change_display">0.00</span> 元
            <input type="hidden" id="change" name="change" value="0">
        </div>

        <div class="button-group">
            <button type="submit" class="btn-confirm">确认收款</button>
            <button type="button" class="btn-cancel" onclick="resetForm()">取消</button>
        </div>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    </form>

    <script>
        // 获取元素
        const productInput = document.getElementById('product_name');
        const quantityInput = document.getElementById('quantity');
        const priceInput = document.getElementById('price');
        const totalSpan = document.getElementById('total_display');
        const totalHidden = document.getElementById('total');
        const paidInput = document.getElementById('paid');
        const changeSpan = document.getElementById('change_display');
        const changeHidden = document.getElementById('change');

        // 当商品从下拉列表选择时，自动填充单价
        productInput.addEventListener('input', function() {
            const selectedOption = document.querySelector(`#product_list option[value="${this.value}"]`);
            if (selectedOption) {
                priceInput.value = selectedOption.dataset.price;
            }
            calculateTotal();
        });

        // 计算应收
        function calculateTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;
            totalSpan.textContent = total.toFixed(2);
            totalHidden.value = total.toFixed(2);
            calculateChange();
        }

        // 计算找补
        function calculateChange() {
            const total = parseFloat(totalHidden.value) || 0;
            const paid = parseFloat(paidInput.value) || 0;
            const change = paid - total;
            if (change >= 0) {
                changeSpan.textContent = change.toFixed(2);
                changeHidden.value = change.toFixed(2);
            } else {
                changeSpan.textContent = '0.00';
                changeHidden.value = '0';
            }
        }

        quantityInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);
        paidInput.addEventListener('input', calculateChange);

        // 重置表单
        function resetForm() {
            document.getElementById('cashierForm').reset();
            totalSpan.textContent = '0.00';
            totalHidden.value = '0';
            changeSpan.textContent = '0.00';
            changeHidden.value = '0';
        }
    </script>
</body>

</html>