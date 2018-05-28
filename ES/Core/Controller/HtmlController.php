<?php
namespace ES\Core\Controller;

use ES\Core\Http\{ResponseTrait,RequestTrait};
use ES\Libraries\HTML\HTML5;
use ES\Core\Toolkit\ConfigStatic;

class HtmlController extends ControllerAbstract
{
    use ResponseTrait,RequestTrait;
    public $title = '';
    public $keywords = '';
    public $description = '';
    public $css = '';
    public $js = '';
    public $viewport = 1;
    
    /**
     * 输出到页面的变量们
     * @param array $data
     */
    protected function __data__(array &$data)
    {
        foreach(['css','js'] as $cj){
            $f = implode('.',[$this->cmdq->d,$this->cmdq->c,$this->cmdq->m,$cj]);
            if($this->cmdq->d!=='esadmin')
                isset($data[$cj]) && $this->$cj .= ','.$data[$cj];
                file_exists(BASEPATH.ConfigStatic::getConfig('bootstrap')."/theme/{$cj}/{$f}") && $this->$cj .= ','.$f;
                ($i = strpos($this->$cj,','))===0 && $this->$cj = substr($this->$cj,$i+1);
                $this->$cj = str_replace('.'.$cj,'',$this->$cj);
        }
        $ref = new \ReflectionClass(__CLASS__);
        foreach( get_class_vars(__CLASS__) as $k =>$v ){
            if( ($ref->getProperty($k)->isPublic() && !isset($data[$k])) || in_array($k,['css','js']) ){
                $data[$k] = $this->$k;
            }
        }
    }
    
    
    /**
     * 快捷视图
     * 默认装入<head>标签中的数据
     * 装入以控制器.方法名 命名的js,css文件，在文件存在的情况下
     *
     * @param array $data        需要到view页面的变量
     * @param bool $hf           开启头尾
     * @param string $layout_dir 页面通用头尾文件夹
     * @return void
     */
    protected function view(array $data=[],bool $hf=TRUE,string $layout_dir='')
    {
        empty($layout_dir) && $layout_dir = $this->cmdq->d;
        is_array($data) || $data = json_decode( json_encode($data) ,TRUE);
        $dir = $this->cmdq->d.'/'.$this->cmdq->c;
        empty($layout_dir) || $dir = $layout_dir;
        $file = "html/{$dir}/{$this->cmdq->c}/{$this->cmdq->m}";
        
        // 载入基本数据,移除冗余数据
        $this->__data__($data);
        
        $data['HTML5'] = (new HTML5)->init($data);
        unset( $data['load'],$data['output'] );
        
        $hf && $this->_load_header_footer($dir,$data);
        $this->load->view($file,$data);
        $hf && $this->_load_header_footer($dir,$data,'footer');
    }
    
    /**
     * 载入头尾页面
     * @param string $dir            头尾页面所在的文件夹
     * @param array $data            需要放置在页面的变量
     * @param string $layout_name    头尾名称
     */
    private function _load_header_footer(string $dir,array $data,string $layout_name='header'){
        foreach( [$this->cmdq->c.'/',''] as $seg ){
            $hf = "{$dir}/{$seg}layout/{$layout_name}";
            if( file_exists(APPPATH.'views/html/'.$hf.'.php') ){
                $this->load->view('html/'.$hf,$data);
                break;
            }
        }
    }
}