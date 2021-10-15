<?php
// +-----------------------------------------------------------
// | 图形验证码
// +-----------------------------------------------------------
// | 人个主页 http://cli.life
// | 堪笑作品 jixiang_f@163.com
// +-----------------------------------------------------------

namespace Namesfang\Captcha\Options;

use Namesfang\Captcha\Debug;

/**
 * 验证码背景参数
 */
class BackgroundOption
{
    /**
     * @var string
     */
    protected $mime = '';

    /**
     * @var array 背景颜色（支持简写）
     */
    protected $color = [];

    /**
     * @var string 背景图片路径
     */
    protected $filename = '';

    /**
     * @var bool 背景是否透明
     */
    protected $transparent = false;

    public function __construct($filename)
    {
        $this->setFilename($filename, false);
    }

    /**
     * @return string
     */
    public function getMime(): string
    {
        return $this->mime;
    }

    /**
     * @return array
     */
    public function getColor(): array
    {
        return $this->color;
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setColor(int $red, int $green, int $blue)
    {
        $this->color = [$red, $green, $blue];
        $this->filename = '';
        $this->transparent = false;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @param bool $check
     * @return false|void
     */
    public function setFilename(string $filename, bool $check=true)
    {
        if($check) {
            if(false === is_file($filename)) {
                return false;
            }
        }
        if (in_array($this->mime = mime_content_type($filename), ['image/png', 'image/jpeg'])) {
            $this->color = [];
            $this->transparent = false;
            $this->filename = $filename;
        }
    }

    /**
     * @return bool
     */
    public function isTransparent(): bool
    {
        return $this->transparent;
    }

    /**
     * @param bool $transparent
     */
    public function setTransparent(bool $transparent)
    {
        $this->transparent = $transparent;
    }
}