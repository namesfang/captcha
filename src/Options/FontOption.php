<?php
// +-----------------------------------------------------------
// | 图形验证码
// +-----------------------------------------------------------
// | 人个主页 http://cli.life
// | 堪笑作品 jixiang_f@163.com
// +-----------------------------------------------------------

namespace Namesfang\Captcha\Options;

/**
 * 验证码字体参数
 */
class FontOption
{
    /**
     * @var string 字体文件路径
     */
    protected $filename;

    /**
     * @var int 强制纠正字体大小（用于中文字体）
     */
    protected $adjustment = 0;

    public function __construct($filename)
    {
        $this->setFilename($filename, false);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename, $check=true)
    {
        if($check) {
            if(false === is_file($filename)) {
                return false;
            }
        }
        $this->filename = $filename;
    }

    /**
     * @return int
     */
    public function getAdjustment()
    {
        return $this->adjustment;
    }

    /**
     * @param int $adjustment
     */
    public function setAdjustment($adjustment)
    {
        $this->adjustment = $adjustment;
    }
}