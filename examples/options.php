<?php
use Namesfang\Captcha\Option;
use Namesfang\Captcha\Bundle;

// +-----------------------------------------------------------
// | 示例：所有可用参数
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

/**
 * 第1种方式
 * 实例化时设置常用参数
 * 3个参数非必填
 */
//$option = new Option(200, 60, 'love');

/**
 * 第2种方式
 * 设置验证码图像宽度
 */
//$option->setWidth(200);

/**
 * 第2种方式
 * 设置验证码图像高度
 */
//$option->setHeight(40);

/**
 * 第2种方式
 * 设置验证码
 * 如不设置验证码，程序会自动
 * 设置后程序会自动计算 length 不需要再次 setLength()
 * 如设置长度少于3则使用 0 补足
 */
//$option->setPhrase('love');

/**
 * 第2种方式
 * 设置验证码长度（用于自动生成）
 * 可用数值 4-6
 * 注意：设置length会自动生成验证码
 */
//$option->setLength(6);

/**
 * 设置验证码文字为单色模式，默认多彩模式
 */
//$option->setPlain();

/**
 * 设置特效 默认开启
 */
//$option->setEffect(false);

/**
 * 设置图片质量
 * 可用数值 0-9
 */
// $option->setQuality(3);

/**
 * 设置自定义字体
 * 如果字体为中文时可能需要设置 $size_adjustment 参数微调
 */
//$option->setFont('path/to/font.ttf');

/**
 * 设置纯色背景
 * 相当于 setBackground(200, 200, 200)
 */
//$option->setBackground(200);

/**
 * 设置纯色背景（16进制）
 * 相当于 setBackgroundHex('3399ff')
 */
//$option->setBackgroundHex('39f');

/**
 * 设置为透明背景
 */
//$option->setBackgroundTransparent();

/**
 * 设置图片
 */
//$option->setBackgroundImage('path/to/image.jpg');

// +-----------------------------------------------------------
// | 验证码缓存并校验
// +-----------------------------------------------------------
/**
 * 将验证码存储到会话
 * 记得先session_start() 一般框架会自动处理
 */
// $_SESSION['captcha'] = $option->getPhrase();
// 校验
//if($_SESSION['captcha'] === $_POST['captcha']) {
//    // 验证成功
//} else {
//    // 验证失败
//}

/**
 * 使用APCu扩展进行缓存
 */
//if(apcu_enabled()) {
//    // 使用APCu扩展进行缓存
//    apcu_store('captcha', $option->getPhrase());
//    // 校验
//}

/**
 * 使用Redis扩展进行缓存
 */
//if(extension_loaded('redis')) {
//    $redis = new \Redis();
//    $redis->connect('127.0.0.1',6379);
//    // 使用Redis扩展进行缓存
//    $redis->set('captcha', $option->getPhrase());
//    // 校验
//    if($redis->get('captcha') === $_POST['captcha']) {
//        // 验证破功
//    } else {
//        // 验证失败
//    }
//    $redis->close();
//}

// +-----------------------------------------------------------
// | 验证码输出到浏览器
// +-----------------------------------------------------------
$bundle = new Bundle($option);

header('Content-Type: image/png');

$bundle->output();


