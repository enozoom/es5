<?php
namespace ES\Core\Load;
use ES\Core\Toolkit\FileStatic;
/**
 * 读取配置文件参数为全局变量$CONFIGS准备配置参数
 * @author Joe e@enozoom.com
 */

final class ConfigStatic{
    static $configs=null;

    /**
     * 初始化配置文件
     * @return \stdClass
     */
    public static function init():\stdClass{
        empty(self::$configs) && self::$configs = self::read();
        return self::$configs;
    }
    
    /**
     * 读取./config下的配置文件
     * @return \stdClass
     */
    final static function read():\stdClass{
        $dir = BASEPATH.'config';
        $configs = new \stdClass();
        $suffix = '.php';
        FileStatic::scanDir($files,$dir,function($f)use($suffix){
            return strpos($f, $suffix)!==FALSE;
        });
        
        foreach($files as $f){
            $k = ucfirst( basename($f,$suffix) );
            $v = include $f;
            $configs->{$k} = $k=='Route'?$v:(object)$v;
        }
        return $configs;
    }
}