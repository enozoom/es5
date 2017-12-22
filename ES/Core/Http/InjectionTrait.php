<?php
/**
 * 将参数注入到页面
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 */
namespace ES\Core\Http;

trait InjectionTrait{
/**
 * 将变量注入到一个模板,并输出模板内容和状态码
 * @param string $tamplate
 * @param array $args
 * @param number $status
 */
    protected function byTamplate($tamplate,Array $args=[],$status=200){
        http_response_code($status);
        die( $this->loadTamplate($tamplate,$args) );
    }
    
    protected function loadTamplate($tamplate,Array $args=[]):string{
        extract($args);
        $path = APPPATH.'Views/'.$tamplate.'.php';
        file_exists( $path ) || $this->tpl_err($path.'不存在','视图文件不存在');
        ob_start();
        include( $path );
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
    
    protected function tpl_err($msg,$tit='503 Service Unavailable',$status=503){
        $this->byTamplate('errors/'.$status,['msg'=>$msg,'tit'=>$tit],$status);
    }
}