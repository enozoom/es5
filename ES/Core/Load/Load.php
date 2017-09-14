<?php
namespace ES\Core\Load;

use ES\Core\Controller\ControllerAbstract;
use ES\Core\Http\InjectionTrait;

class Load
{
    use InjectionTrait;
    
    private $dir_model   = 'models/';
    private $dir_view    = 'views/';
    private $dir_library = 'libraries/';
    
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
        if(!property_exists(ControllerAbstract::getInstance(),$alias)){
            foreach( [APPPATH,SYSPATH] as $path ){
                if( file_exists( $file = $path.$this->dir_library.str_replace('\\','/',$cls).'.php' ) ){
                    $cls = str_replace($clsName, ucfirst($clsName), substr(str_replace('/', '\\', $file),0,-4) );
                    $this->Ctrl()->$alias = new $cls();
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
            $cls = str_replace('/','\\',APPPATH.$this->dir_model).ucfirst($cls);
            $_model = new $cls();
            is_subclass_of($_model,'\es\core\Model\ModelAbstract') || $this->tpl_err($cls.'非ES_model子类');
            $this->Ctrl()->$alias = &$_model;
        }
        return $this;
    }
    
    public function view($tpl='',Array $args=[])
    {
        $htm = $this->loadTamplate($tpl,$args);
        $this->Ctrl()->output->append_output($htm);
    }
    
    
    private function &Ctrl():ControllerAbstract
    {
        return ControllerAbstract::getInstance();
    }
}