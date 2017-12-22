<?php
namespace ES\Core\Http;

use ES\Core\Toolkit\ConfigStatic;

trait ResponseTrait{
    use InjectionTrait,HeaderTrait;
    
    /**
     * 服务器错误
     * @param string $msg
     * @param string $tit
     */
    protected function show_503(string $msg='',string $tit='503 Service Unavailable')
    {
        $this->_tpl_err($msg,$tit);
    }
    
    /**
     * 页面不存在
     * @param string $msg
     * @param string $tit
     */
    protected function show_404(string $msg='',string $tit='404 Not Found')
    {
        $this->_tpl_err($msg,$tit,404);
    }
    
    /**
     * 禁止访问
     * @param string $msg
     * @param string $tit
     */
    protected function show_403(string $msg='Directory access is forbidden.',string $tit='403 Forbidden')
    {
        $this->_tpl_err($msg,$tit,403);
    }
    
    /**
     *
     * @param string $msg  显示在错误页的具体内容
     * @param string $tit     显示在错误页的标题
     * @param number $status
     */
    protected function _tpl_err(string $msg,string $tit='503 Service Unavailable',int $status=503)
    {
        if( ConfigStatic::getConfig('debug') )
        {
            $this->tpl_err($msg,$tit,$status);
        }
        else
        {
            $this->tpl_err('<p>当前页面禁止访问！</p><p><small>'.ES_POWER.'</small></p>','禁止访问',403);
        }
    }
    
/**
 * 输出到浏览器，并停止运行
 * @param string $str                   输出内容
 * @param string $type                输出类型
 * @param int $httpcacheHours   缓存时间
 */
    protected function render($str='',$type='html',$httpcacheHours=24)
    {
        $compress = ConfigStatic::getConfig('compress');
        $flag = $compress && strlen($str)>1024 && extension_loaded('zlib');
        $this->httpCache($httpcacheHours);
        $this->httpMime($type);
        $flag && ob_start('ob_gzhandler');
            echo $str;
        $flag && ob_end_flush();
        die();
    }

}