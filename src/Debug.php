<?php
// +-----------------------------------------------------------
// | 图形验证码
// +-----------------------------------------------------------
// | 人个主页 http://cli.life
// | 堪笑作品 jixiang_f@163.com
// +-----------------------------------------------------------

namespace Namesfang\Captcha;

/**
 * 调试打印使用
 */
class Debug
{
    /**
     * 是否使用 HTML pre
     * @var bool
     */
    protected static $preformatted = false;

    /**
     * 使用HTML标签 pre 包裹打印内容
     * @param mixed ...$vars
     * @return Debug
     */
    public static function pre(...$vars): Debug
    {
        self::$preformatted = true;
        return self::dd('print_r', true, ...$vars);
    }

    /**
     * 打印方式 print_r
     * @param ...$vars
     * @return Debug
     */
    public static function print(...$vars): Debug
    {
        return self::dd('print_r', self::$preformatted, ...$vars);

    }

    /**
     * 打印方式 var_dump
     * @param ...$vars
     * @return Debug
     */
    public static function dump(...$vars): Debug
    {
        return self::dd('var_dump', self::$preformatted, ...$vars);
    }

    /**
     * 打印并退出
     */
    public static function exit(...$vars)
    {
        if ($vars) {
            self::pre(...$vars);
        }
        exit;
    }

    /**
     * 输出
     * @param callable $func 用于输出打印的函数
     * @param bool $preformatted 使用 HTML pre标签输出
     * @param mixed ...$vars 要打印的变量
     * @return Debug
     */
    public static function dd(callable $func, bool $preformatted, ...$vars): Debug
    {
        if ($preformatted) {
            echo '<pre>';
        }
        foreach ($vars as $var) {
            $func($var);
            if ($preformatted) {
                echo '<br>';
            } else {
                echo "\r\n";
            }
        }
        if ($preformatted) {
            echo '</pre>';
        }
        return new static;
    }
}