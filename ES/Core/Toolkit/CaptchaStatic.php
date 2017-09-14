<?php
namespace ES\Core\Toolkit;

use function es\helpers\isMobile;

final class CaptchaStatic
{
  /**
   * 发送验证码
   * @param string $mobile
   * @param string $smstpl
   */
    public static function sendCaptcha_(string $mobile,string $smstpl):bool
    {
        isMobile($mobile);
    }
  /**
   * 验证验证码
   * @param string $mobile
   * @param string $smstpl
   * @param string $code
   */
    public static function checkCaptcha_(string $mobile,string $smstpl,$code):bool
    {
        isMobile($mobile);
    }
}