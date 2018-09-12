<?php
namespace ES\Core\Load;

use ES\Core\Controller\ControllerAbstract;
use ES\Core\Http\InjectionTrait;

class Load
{
    use InjectionTrait;
    
    private $dir_model   = 'models';
    
    /**
     * 动态加载一个可以在前台使用的方法
     * 如：在controllers/A.php中
     * class A extends Controller{
     *    public function m(){
     *       $this->load->func = function($args){
     *          // $args = ['arg','arg2']
     *          return FALSE;
     *       }
     *
     *    }
     * }
     * 在views/A/m.php中就可以直接使用
     * $this->func('arg','arg2');
     *
     * @param string $method
     * @param [] $args
     */
    public function __call($method, $args)
    {
        if (isset($this->$method)) {
            $func = $this->$method;
            return $func($args);
        }
    }
    
    public function library($cls,$alias=FALSE):Load
    {
        $clsName = preg_replace('|(\w+[^\w]+)*(\w+)$|', '$2', $cls);
        empty($alias) && $alias = $clsName;
        $syspath = BASEPATH.'ES/';
        if(!property_exists(ControllerAbstract::getInstance(),$alias)){
            $paths = [ '\\app\\libraries\\'.$cls, '\\ES\\Libraries\\'.$cls ];
            foreach($paths as $path){
                $_path = '.'.str_replace('\\', '/', $path).'.php';
                if(file_exists($_path)){
                    $this->Ctrl()->$alias = new $path();
                    break;
                }
            }
        }
        empty($this->Ctrl()->$alias) && $this->tpl_err('Library:'.$cls.' not exists!');
        return $this;
    }
    
    public function model($cls,$alias=FALSE):Load
    {
        empty($alias) && $alias = $cls;
        if(!property_exists(ControllerAbstract::getInstance(),$alias)){
            $cls = implode('\\', [basename(APPPATH),$this->dir_model,$this->decodePath($cls)]);
            $_model = new $cls();
            is_subclass_of($_model,'\ES\core\Model\ModelAbstract') || $this->tpl_err($cls.'非ES_model子类');
            $this->Ctrl()->$alias = &$_model;
        }
        return $this;
    }
    
    public function view($tpl='',array $args=[])
    {
        $htm = $this->loadTamplate($tpl,$args);
        $this->Ctrl()->output->append_output($htm);
    }
    
    private function decodePath(string $path):string
    {
        $path = str_replace('\\', '/', $path);
        if(strpos($path, '/')!==FALSE){
            $arr = explode('/', $path);
            $last = count($arr)-1;
            $arr[$last] = ucfirst($arr[$last]);
            $path = implode('\\', $arr);
        }
        return $path;
    }
    
    private function &Ctrl():ControllerAbstract
    {
        return ControllerAbstract::getInstance();
    }
}