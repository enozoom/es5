<?php
/*
 * 字符串相关操作
 */
namespace ES\Core\Toolkit;

final class StrStatic {
    /**
     * 去字与字之间的空白.
     * @param $str string
     * @return string
     */
    public static function cleanWordblank(string $str):string{
        $str = preg_replace('|(\s*)(\S+)(\s*)(\S+)(\s*)|','$2$4',$str);
        // 解决中文空格和换表符无法正确匹配的问题
        $str = str_replace(['  ','　','  ','　'], '', $str);
        return self::removeInvisibleCharacters($str);
    }
    
    /**
     * 去除非法字符
     * @access public
     * @param  string $str
     * @return string $url_encoded
     */
    public static function removeInvisibleCharacters(string $str, $url_encoded = TRUE)
    {
        $non_displayables = [];
    
        if ($url_encoded){
            $non_displayables[] = '/%0[0-8bcef]/';  // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';  // url encoded 16-31
        }
    
        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';  // 00-08, 11, 12, 14-31, 127
    
        do{
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        }while ($count);
    
        return $str;
    }
    
    /**
     * 产生随机数
     * @param string $type alnum:数字+大小写字母;numeric:数字;alpha:大小写字母
     * @param int    $len
     *
     * @return string
     */
    public static function randomString(int $len=6,string $type='alnum'):string{
        $numeric = '0123456789';
        $alpha = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $alnum = $numeric.$alpha;
        
        return substr( str_shuffle($$type.$$type), 0,$len);
    }
    
    /**
     * 伪造一个电话号码
     * @param bool 是否遮挡中间部分
     * @return string
     */
    public static function forgedMobile(bool $defend=TRUE):string{
        return sprintf('1%d%s%s',
                       [3,5,7,8][mt_rand(0,3)].mt_rand(0,9),
                       $defend?'****':str_pad(mt_rand(1000,9999),4,'0',STR_PAD_LEFT),
                       str_pad(mt_rand(0,9999),4,'0',STR_PAD_LEFT));
    }
    
    /**
     * 判断是否是一个手机号
     * @param string $mobile
     * @return bool
     */
    public static function isMobile(string $mobile=''):string{
        return preg_match('/^1[34578]\d{9}$/', $mobile);
    }
    
    /**
     * 判断是否是中文
     * @param string $txt
     * @return bool
     */
    public static function isChinese(string $txt):bool{
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $txt);
    }
}