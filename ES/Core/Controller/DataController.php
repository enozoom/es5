<?php
/**
 * 单纯的数据操作控制器
 * @author Joe e@enozoom.com
 * Powered by Enozoomstudio
 * 2016年6月22日上午9:46:38
 */
namespace ES\Core\Controller;

use ES\Core\Http\{RequestTrait,ResponseTrait};
use ES\Core\Toolkit\AryStatic;
class DataController extends ControllerAbstract{
    use RequestTrait,ResponseTrait{ render as private oRender; }
    
    protected function render($str='',$type='html',$httpCache=24){
        $this->oRender($str,$type,$httpCache);
        $this->closeDB();
        exit();
    }
    
    /**
     * 获取POST提交数据且必须包含$requires中存在的数据
     * @param array $requires
     * @return bool
     */
    protected function __postRequires($requires=[]){
        return $this->reqestMethod('post') && AryStatic::isRequired($requires);
    }
    
    /**
     * logger->debug的别名
     * @param string $msg
     */
    protected function __log($msg){
        \ES\Core\Toolkit\ConfigStatic::getConfigs('Logger')->debug($msg);
    }
}