<?php
/**
 * ES5.0170911
 * @author Joe e@enozoom.com
 * 2016年6月24日09:44:36
 */
use ES\Core\Log\Logger;
use ES\Core\Hook\SystemHook;
use ES\Core\Http\Cmdq;
use ES\Core\Route\Route;
use ES\Core\Load\ConfigStatic;

// 自动加载
spl_autoload_register(function($classname){
    if( strpos($classname,'\\') ){
        $_path = BASEPATH.str_replace('\\', '/', $classname).'.php';
        if( file_exists($_path) ){
            require $_path;
            return '';
        }
    }
});

$CONFIGS = ConfigStatic::init();
$CONFIGS->Logger = Logger::getInstance();

 // 是否开启调试
 error_reporting($CONFIGS->Config->debug?E_ALL:0);

// 系统前加载
$CONFIGS->Hook = new SystemHook;
$CONFIGS->Hook->beforeController();

// 请求
$CONFIGS->Cmdq = (new Cmdq())->get();


// 路由
// 系统加载完成后执行
(new Route())->initController();