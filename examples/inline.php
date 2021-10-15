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
// | 验证码使用（Data URI scheme）
// +-----------------------------------------------------------
$bundle = new Bundle($option);

/**
 * 后端验证码接口
 */
echo json_encode([
    'code'=> 0,
    'msg'=> 'ok',
    'data'=> $bundle->inline(), // data:image/png;base64,xxxxxx
]);

/**
 * 前端js从后端获取验证码数据
 */
//jQuery.get('path/to/inline.php', function (pl) {
//    if(0 === pl.code) {
//        $('#captcha_container').html('<img src="'+ pl.data +'"/>');
//    }
//});