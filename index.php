<?php
/**
 * 入口文件
 */
//检测php版本
header("Content-type: text/html; charset=utf-8");
if (version_compare(PHP_VERSION, '5.5', '<')) {
    die('PHP版本过低，最少需要PHP5.5，请升级PHP版本！');
}

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');

// 定义后台入口文件
define('ADMIN_FILE', 'admin.php');

// 加载框架引导文件
require './thinkphp/start.php';