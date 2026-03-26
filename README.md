# cashier-system

基于[PHP-register](https://github.com/GaoCi9008/PHP-register)的延续
收银系统由多个 PHP 文件组成，实现了登录认证、商品选择、订单结算、会话管理等完整收银流程。以下是各核心文件的功能与关键代码解析。

1. config.php – 统一数据库与会话配置
作用：集中管理数据库连接参数、PDO 初始化，并设置会话安全选项（HttpOnly、use_only_cookies）。

2. login.php + login_view.php – 登录逻辑与视图
作用：处理收银员登录验证，限制错误尝试次数（可选），并生成 CSRF 令牌防止跨站请求伪造。

安全增强：

登录成功后调用 session_regenerate_id(true) 防止会话固定攻击。

使用 password_verify() 验证密码哈希。

支持登录失败次数限制（可在配置中启用/禁用）。

视图文件：提供简洁的登录表单，包含隐藏的 csrf_token 字段。

3. cashier.php + cashier_view.php – 收银主逻辑与界面
作用：

cashier.php：检查登录状态，获取商品列表，处理 POST 请求中的订单数据，执行数据库事务（订单主表 + 订单明细表），并给出操作反馈。

cashier_view.php：显示商品选择下拉框、数量/单价输入、应收/实收/找补计算（JavaScript 前端动态计算），以及确认/取消按钮。

核心功能：

下拉列表从 products 表读取商品名和单价，支持手动输入新商品。

前端实时计算应收金额和找补。

提交时验证 CSRF 令牌，确保请求合法性。

使用事务保证订单与明细的一致性。

4. logout.php – 安全退出
作用：销毁当前会话并重定向到登录页。


5. 数据库表结构（共 4 张表）
建表 SQL 示例（MySQL）：

sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) NOT NULL,
    change_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
🖥️ 运行环境与软件
组件	版本/说明
操作系统	Windows 7/10/11（32位或64位）
Web 服务器	 PHP 内置服务器
PHP 版本	PHP 7.0 及以上（推荐 PHP 8.4）
PHP 扩展	pdo_mysql（必须）、mysqli（可选）
数据库	MySQL 5.7 或 8.0
开发工具	Visual Studio Code
运行环境	XAMPP / phpStudy（一键集成）或手动配置 PHP + MySQL
🚀 部署与运行步骤

先下载PHP服务器，随后在文件目录cmd窗口启动PHP服务器。
 
创建数据库

在 MySQL 中创建数据库 job_db（如果尚未存在）。

执行上述建表语句，创建 products、orders、order_items 表（users 表已在注册系统存在）。

插入示例商品数据（可选）。

配置连接

编辑 config.php，修改数据库连接参数（主机、用户名、密码、数据库名）以匹配你的环境。

启动应用

浏览器访问 http://localhost/pos_system/login.php。

使用已注册的收银员账号登录（可从 users 表添加或通过注册页面创建）。

登录后进入收银界面，选择商品、输入数量、实收金额，点击“确认收款”保存订单。

点击“退出登录”可安全退出。

测试验证

检查数据库中的 orders 和 order_items 表，确认订单数据已正确保存。

测试未登录情况下直接访问 cashier.php，应自动跳转到登录页。

🛡️ 安全措施总结
安全措施	实现方式
SQL 注入防护	使用 PDO 预处理语句
跨站脚本（XSS）防护	所有输出使用 htmlspecialchars() 转义
跨站请求伪造（CSRF）防护	每个表单生成唯一令牌并验证
密码安全	使用 password_hash() 加密存储，登录时 password_verify() 验证
会话安全	session.cookie_httponly=1、session.use_only_cookies=1，登录后重新生成会话 ID
访问控制	未登录用户无法访问收银页面
错误处理	数据库异常仅显示通用提示，不暴露具体信息
📚 扩展方向
商品管理：增加商品增删改查页面。

订单查询：提供历史订单列表，按日期或收银员筛选。

打印小票：订单成功后生成 HTML 小票并调用浏览器打印。

权限分级：区分管理员与普通收银员，管理员可管理商品、查看报表。

HTTPS 部署：生产环境启用 SSL/TLS 加密传输。
