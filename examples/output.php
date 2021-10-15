<?php
use Namesfang\Captcha\Option;
use Namesfang\Captcha\Bundle;

// +-----------------------------------------------------------
// | 示例：图形验证码输出到浏览器
// | 完整示例请看 options.php
// +-----------------------------------------------------------

/**
 * 以下为示例所需
 * 实际使用时框架不需要引入
 */
define('ROOT_PATH', dirname(__DIR__));

spl_autoload_register(function ($className) {
    $className = str_replace('\\', '/', $className);
    $className = str_replace('Namesfang/Captcha/', '', $className);
    require_once sprintf('%s/src/%s.php', ROOT_PATH, $className);
});

// +-----------------------------------------------------------
// | 参数配置
// +-----------------------------------------------------------
$option = new Option();

// +-----------------------------------------------------------
// | 验证码输出到浏览器
// | 完整示例请看 options.php
// +-----------------------------------------------------------
// | 在html中使用img标签引用当前地址
// | <img src="path/to/examples/output.php">
// +-----------------------------------------------------------
$bundle = new Bundle($option);

header('Content-Type: image/png');

$bundle->output();

