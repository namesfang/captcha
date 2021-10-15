<?php
// +-----------------------------------------------------------
// | 图形验证码
// +-----------------------------------------------------------
// | 人个主页 http://cli.life
// | 堪笑作品 jixiang_f@163.com
// +-----------------------------------------------------------

namespace Namesfang\Captcha;

class Bundle
{
    /**
     * @var number 验证码最大倾斜角度
     */
    protected $maxAngle = 8;

    /**
     * @var number 验证码最大偏移量
     */
    protected $maxOffset = 5;

    /**
     * @var bool 文字扭曲
     */
    protected $distortion = true;

    /**
     * 二维码图片
     * @var resource $image
     */
    protected $image;

    /**
     * 参数
     * @var Option
     */
    public $option;

    public function __construct(Option $option)
    {
        $this->option = $option;

        // 图像背景时强制关闭扭曲效果
        if ($this->option->getBackground()->getFilename()) {
            $this->distortion = false;
        }

        // 生成验证码
        $this->drawCaptcha();
    }

    /**
     * 直接输出原始图像流
     */
    public function output()
    {
        imagepng($this->image, null, $this->option->getQuality());
    }

    /**
     * 获得原始图像流
     * @return string
     */
    public function stream(): string
    {
        ob_start();

        $this->output();

        return ob_get_clean();
    }

    /**
     * 验证码图片
     * @return string
     */
    public function inline(): string
    {
        $stream = $this->stream();

        $base64 = base64_encode($stream);

        return 'data:image/jpeg;base64,' . $base64;
    }

    protected function drawCaptcha()
    {
        $width = $this->option->getWidth();
        $height = $this->option->getHeight();
        $length = $this->option->getLength();

        // 启用背景图片
        $identifier = null;

        $image = imagecreatetruecolor($width, $height);

        $background = $this->option->getBackground();

        // 背景色或图片
        $bc = null;

        // 非透明背景时
        if (false === $background->isTransparent()) {

            // 纯色背景
            if (count($bc = $background->getColor())) {
                $identifier = imagecolorallocate($image, $bc[0], $bc[1], $bc[2]);
                imagefill($image, 0, 0, $identifier);
            } // 图像背景
            else if (is_string($bc = $background->getFilename())) {
                if ($background->getMime() == 'image/png') {
                    $bc = imagecreatefrompng($bc);
                } else {
                    $bc = imagecreatefromjpeg($bc);
                }
                imagecopy($image, $bc, 0, 0, 0, 0, $width, $height);
            }
        }

        // 透明背景或未匹配到背景色图片时生成透明背景
        if (is_null($bc) || $background->isTransparent()) {
            imagealphablending($image, true);
            imagesavealpha($image, true);
            $identifier = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $identifier);
        }

        if (is_resource($bc)) {
            imagedestroy($bc);
        }

        // 有效的验证码
        if ($length) {
            // 绘制背景线
            $this->drawLines($image, $width, $height, $length);

            // 绘制验证码
            $this->writePhrase($image, $width, $height, $length);

            // 绘制前景线
            $this->drawLines($image, $width, $height, $length);

            // 其他特效
            if ($this->option->isEffect()) {
                $this->postEffect($image);
            }
            // 扭曲特效
            if ($this->distortion) {
                $image = $this->distort($image, $identifier, $width, $height);
            }
        }

        $this->image = $image;
    }

    /**
     * 生成干扰线
     * @param $image
     * @param $width
     * @param $height
     */
    protected function drawLines($image, $width, $height)
    {
        $length = mt_rand(1, 3);

        while ($length > -1) {
            $color = imagecolorallocate($image, mt_rand(100, 255), mt_rand(100, 255), mt_rand(100, 255));

            if (mt_rand(0, 1)) { // Horizontal
                $Xa = mt_rand(0, $width / 2);
                $Ya = mt_rand(0, $height);
                $Xb = mt_rand($width / 2, $width);
                $Yb = mt_rand(0, $height);
            } else { // Vertical
                $Xa = mt_rand(0, $width);
                $Ya = mt_rand(0, $height / 2);
                $Xb = mt_rand(0, $width);
                $Yb = mt_rand($height / 2, $height);
            }

            imagesetthickness($image, mt_rand(1, 3));
            imageline($image, $Xa, $Ya, $Xb, $Yb, $color);

            $length--;
        }
    }

    /**
     * 绘制验证码
     * @param $image
     * @param $width
     * @param $height
     * @param $length
     */
    protected function writePhrase($image, $width, $height, $length)
    {
        $plain = $this->option->isPlain();

        $phrase = $this->option->getPhrase();

        $font = $this->option->getFont();
        $filename = $font->getFilename();

        // 微调数值
        $adjustment = $font->getAdjustment();

        $size = $width / $length - mt_rand(0, 3) - $adjustment;

        $box = \imagettfbbox($size, 0, $filename, $phrase);

        $textWidth = $box[2] - $box[0];
        $textHeight = $box[1] - $box[7];
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2 + $size;

        $color = \imagecolorallocate($image, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));

        for ($i = 0; $i < $length; $i++) {

            $text = mb_substr($phrase, $i, 1);

            $box = \imagettfbbox($size, 0, $filename, $text);

            $w = $box[2] - $box[0];

            $angle = mt_rand(-$this->maxAngle, $this->maxAngle);

            $offset = mt_rand(-$this->maxOffset, $this->maxOffset);

            \imagettftext($image, $size, $angle, $x, $y + $offset, $color, $filename, $text);

            $x += $w;

            // 多彩模式
            if (false === $plain) {
                $color = \imagecolorallocate($image, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
            }
        }
    }

    /**
     * 扭曲
     * @param resource $image
     * @param $color
     * @param number $width
     * @param number $height
     * @return resource
     */
    protected function distort($image, $color, $width, $height)
    {
        $contents = imagecreatetruecolor($width, $height);
        $X = mt_rand(0, $width);
        $Y = mt_rand(0, $height);
        $phase = mt_rand(0, 10);
        $scale = 1.1 + mt_rand(0, 10000) / 30000;
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $Vx = $x - $X;
                $Vy = $y - $Y;
                $Vn = sqrt($Vx * $Vx + $Vy * $Vy);

                if ($Vn != 0) {
                    $Vn2 = $Vn + 4 * sin($Vn / 30);
                    $nX = $X + ($Vx * $Vn2 / $Vn);
                    $nY = $Y + ($Vy * $Vn2 / $Vn);
                } else {
                    $nX = $X;
                    $nY = $Y;
                }
                $nY = $nY + $scale * sin($phase + $nX * 0.2);

                $p = $this->interpolate(
                    $nX - floor($nX),
                    $nY - floor($nY),
                    $this->getCol($image, floor($nX), floor($nY), $color),
                    $this->getCol($image, ceil($nX), floor($nY), $color),
                    $this->getCol($image, floor($nX), ceil($nY), $color),
                    $this->getCol($image, ceil($nX), ceil($nY), $color)
                );

                if ($p == 0) {
                    $p = $color;
                }

                imagesetpixel($contents, $x, $y, $p);
            }
        }

        return $contents;
    }

    /**
     * 特效
     * @param $image
     */
    protected function postEffect($image)
    {
        if (!function_exists('imagefilter')) {
            return;
        }

        $rand = mt_rand(0, 9);

        if ($rand === 0) {
            imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
        }

        if ($rand === 1) {
            imagefilter($image, IMG_FILTER_EDGEDETECT);
        }

        if ($rand === 2) {
            imagefilter($image, IMG_FILTER_MEAN_REMOVAL);
            imagefilter($image, IMG_FILTER_COLORIZE, mt_rand(-80, 50), mt_rand(-80, 50), mt_rand(-80, 50));
        }
    }

    /**
     * @param $x
     * @param $y
     * @param $nw
     * @param $ne
     * @param $sw
     * @param $se
     * @return int
     */
    protected function interpolate($x, $y, $nw, $ne, $sw, $se): int
    {
        list($r0, $g0, $b0) = $this->getRGB($nw);
        list($r1, $g1, $b1) = $this->getRGB($ne);
        list($r2, $g2, $b2) = $this->getRGB($sw);
        list($r3, $g3, $b3) = $this->getRGB($se);

        $cx = 1.0 - $x;
        $cy = 1.0 - $y;

        $m0 = $cx * $r0 + $x * $r1;
        $m1 = $cx * $r2 + $x * $r3;
        $r = (int)($cy * $m0 + $y * $m1);

        $m0 = $cx * $g0 + $x * $g1;
        $m1 = $cx * $g2 + $x * $g3;
        $g = (int)($cy * $m0 + $y * $m1);

        $m0 = $cx * $b0 + $x * $b1;
        $m1 = $cx * $b2 + $x * $b3;
        $b = (int)($cy * $m0 + $y * $m1);

        return ($r << 16) | ($g << 8) | $b;
    }

    /**
     * @param $image
     * @param $x
     * @param $y
     * @param $background
     * @return int
     */
    protected function getCol($image, $x, $y, $background): int
    {
        $L = imagesx($image);
        $H = imagesy($image);
        if ($x < 0 || $x >= $L || $y < 0 || $y >= $H) {
            return $background;
        }

        return imagecolorat($image, $x, $y);
    }

    /**
     * @param $col
     * @return array
     */
    protected function getRGB($col): array
    {
        return [
            $col >> 16 & 0xff,
            $col >> 8 & 0xff,
            (int)($col) & 0xff,
        ];
    }
}