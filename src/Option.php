<?php
// +-----------------------------------------------------------
// | 图形验证码
// +-----------------------------------------------------------
// | 人个主页 http://cli.life
// | 堪笑作品 jixiang_f@163.com
// +-----------------------------------------------------------

namespace Namesfang\Captcha;

use Namesfang\Captcha\Options\BackgroundOption;
use Namesfang\Captcha\Options\FontOption;

/**
 * 验证码主参数
 */
class Option
{
    /**
     * @var int 验证码图片宽
     */
    protected $width;

    /**
     * @var int 验证码图片高
     */
    protected $height;

    /**
     * @var string 验证码
     */
    protected $phrase;

    /**
     * @var int 验证码长度
     */
    protected $length = 4;

    /**
     * @var bool 验证码纯色或多彩
     */
    protected $plain = false;

    /**
     * @var FontOption 验证码字体
     */
    protected $font;

    /**
     * @var BackgroundOption 验证码背景色
     */
    protected $background;

    /**
     * @var bool 特效
     */
    protected $effect = false;

    /**
     * @var $quality 图像质量 0-9
     */
    protected $quality = 9;

    /**
     * 初始化必要参数
     * @param int $width 验证码宽度
     * @param int $height 验证码高度
     * @param string $phrase 验证码 为空时自动生成 设置验证码时 length自动获取
     */
    public function __construct(int $width = 150, int $height = 40, string $phrase = null)
    {
        $this->setWidth($width);
        $this->setHeight($height);

        if ($phrase) {
            $this->setPhrase($phrase);
        } else {
            $this->makePhrase();
        }

        $index = mt_rand(0, 4);

        $path = dirname(__DIR__) . '/resources';

        $this->font = new FontOption($path . '/fonts/' . $index . '.ttf');
        $this->background = new BackgroundOption($path . '/backgrounds/' . $index . '.jpg');
    }

    /**
     * 自动生成验证码
     */
    protected function makePhrase()
    {
        if($this->length) {
            $this->phrase = substr(str_shuffle('23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ'), 0, $this->length);
        }
    }

    /**
     * @param integer $width
     */
    public function setWidth(int $width)
    {
        $this->width = min(320, max(90, $width));
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param $height
     */
    public function setHeight($height)
    {
        $this->height = min(120, max(30, $height));
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param string $phrase 设置验证码时 length自动获取
     */
    public function setPhrase(string $phrase)
    {
        $this->length = mb_strlen($phrase, 'utf-8');

        // 最少4个字符
        if($this->length < 4) {
            $this->phrase = $phrase . substr('0000', 0, 4-$this->length);
            $this->length = 4;
        } else {
            $this->phrase = $phrase;
        }
    }

    /**
     * @return string
     */
    public function getPhrase(): string
    {
        return $this->phrase;
    }

    /**
     * @return bool
     */
    public function isPlain(): bool
    {
        return $this->plain;
    }

    /**
     * @param bool $plain
     */
    public function setPlain(bool $plain=true)
    {
        $this->plain = $plain;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * 设置时会重新生成验证码
     * @param int $length
     */
    public function setLength(int $length)
    {
        $this->length = min(6, max(4, $length));
        $this->makePhrase();
    }

    /**
     * @return bool
     */
    public function isEffect(): bool
    {
        return $this->effect;
    }

    /**
     * @param bool $effect
     */
    public function setEffect(bool $effect)
    {
        $this->effect = $effect;
    }

    /**
     * @return int
     */
    public function getQuality(): int
    {
        return $this->quality;
    }

    /**
     * @param int $quality 图像质量
     */
    public function setQuality(int $quality)
    {
        $this->quality = min(9, max(1, $quality));
    }

    /**
     * @param $filename
     * @param int $size_adjustment 微调字体尺寸
     */
    public function setFont($filename, int $size_adjustment = 0)
    {
        $this->font->setFilename($filename);
        $this->font->setAdjustment($size_adjustment);
    }

    /**
     * @return FontOption
     */
    public function getFont(): FontOption
    {
        return $this->font;
    }

    /**
     * 设置验证码背景色（透明背景无效）
     * @param ...$rgb 顺序 红,绿,蓝或简写180
     */
    public function setBackground(...$rgb)
    {
        if(1 === count($rgb)) {
            $rgb = array_fill(0, 3, array_pop($rgb));
        }
        if (3 === count($rgb)) {
            $this->background->setColor(...$rgb);
        }
    }

    /**
     * 设置验证码16进制背景颜色（透明背景无效）
     * @param string $hex 16进制 3399ff或简写39f
     */
    public function setBackgroundHex(string $hex)
    {
        // 39f 3399ff
        if(3 === strlen($hex)) {
            $hex = array_map(
                function ($item) {
                    return $item . $item;
                },
                str_split($hex, 1)
            );
        } else {
            $hex = str_split(substr($hex, 0, 6), 2);
        }

        if(3 === count($hex)) {
            $this->background->setColor(...array_map(
                function ($item) {
                    return hexdec($item);
                },
                $hex
            ));
        }
    }

    /**
     * 设置验证码背景图片 默认使用内置的5张随机展示（透明背景无效）
     * @param string $filename 背景图片路径 仅支持 png,jpeg
     */
    public function setBackgroundImage(string $filename)
    {
        if(false === $this->background->isTransparent()) {
            $this->background->setFilename($filename);
        }
    }

    /**
     * 设置验证码为透明背景
     */
    public function setBackgroundTransparent()
    {
        $this->background->setTransparent(true);
    }

    /**
     * 获得验证码背景参数
     * @return BackgroundOption
     */
    public function getBackground(): BackgroundOption
    {
        return $this->background;
    }
}