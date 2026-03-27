<?php
session_start();
// 销毁所有会话变量
session_unset();
// 销毁会话本身
session_destroy();
// 重定向到登录页
header('Location: login.php');
exit;
