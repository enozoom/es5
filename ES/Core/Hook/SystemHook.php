<?php
namespace ES\Core\Hook;

class SystemHook implements HookInterface{

    public function beforeController()
    {
        // 设定时区
        date_default_timezone_set('Asia/Chongqing');
        // 版本检查
        $php_version = '7.0.0';
        if(!(version_compare(PHP_VERSION, $php_version, '>=') )){
            die( sprintf('Requires PHP version >= %s, Your version: ',$php_version, PHP_VERSION ));
        }
        // 防御XSS攻击
        \ES\Core\Toolkit\XssStaitc::defense();
    }
    
    public function afterController()
    {
        
    }
    
    public function afterControllerMethod()
    {
    }
}