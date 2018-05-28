<?php
namespace ES\Libraries\HTML;

class HTML5 implements HTMLInterface
{
    private $title='';
    private $css='';
    private $js='';
    private $keywords='';
    private $description='';
    private $viewport=0;
    private $noindex=0;
    
    public function init(array &$args = [])
    {
        $params = get_class_vars(__CLASS__);
        foreach($args as $k=>$v){
            if(key_exists($k, $params)){
                $this->{$k} = $v;
                unset($args[$k]);
            }
        }
        return $this;
    }

    public function display(){}
    
    public function header($print=1){
        $viewport = $ie = $noindex = $keywords = $description = $css = '';
        $html = <<<HTML5
<!DOCTYPE html><html lang="cmn-hans">
<head><meta charset="utf-8" />
<title>{$this->title}</title>
<meta name="renderer" content="webkit">
<meta name="author" content="JOE POWERED BY ENOZOOMstudio"/>
%s%s%s%s%s%s
</head><body>
HTML5;

        if($this->viewport){
            $viewport = '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">';
        }
        
        $this->noindex && $noindex = '<meta name="robots" content="noindex,nofollow" />';
        $this->keywords && $keywords = sprintf('<meta name="keywords" content="%s"/>',$this->keywords);
        $this->description && $description = sprintf('<meta name="description" content="%s"/>',$description);
        $this->css && $css = $this->resolve_http($this->css);

        $html = sprintf($html,$viewport,$ie,$noindex,$keywords,$description,$css);
        $print && print($html);
        return $html;
    }
    
    public function footer($print=1){
        $html = (empty($this->js)?'':$this->resolve_http($this->js,'js')).'</body></html>';
        $print && print($html);
        return $html;
    }
    
    
    /**
     * 导入style文件,不需要添加.css后缀,支持本域使用数组形式导入CSS文件,不支持其他域.
     *
     * @param array $file 文件
     * @param $ie 对IE进行兼容
     * @param $foreign 是否来自外面的链接
     * @return HTMLString
     *
     */
    
    private function head_link($file,$ie=FALSE,$foreign=FALSE){
        $href =  ($foreign?'':'/Min/').$file.'.css';
        $link = sprintf('<link href="%s" rel="stylesheet">',$href);
        return empty($ie)?$link:'<!--[if '.$ie.']>'.$link.'<![endif]-->';
    }
    
    /**
     * 导入js文件,不需要添加.js后缀,支持本域使用数组形式导入JS文件,不支持其他域数组形式导入.
     * @example:
     * 表现在页面html代码为:
     * 1.<script src='urjsfile.js'></script>
     * 2.
     *  <!--[if $ie]>
     *    <script src='urjsfile.js'></script>
     *  <![endif]-->
     *
     * @param $file array | 文件
     * 如
     * @param $ie 对IE进行兼容
     * $ie = 'IE,IE 6,IE 7,IE 8,IE 9,gte IE 8,lt IE 9,lte IE 7,gt IE 6,!IE'
     * 如head_script('global','lt IE 9');
     * @param $foreign 是否来自外面的链接
     * @return HTMLString
     *
     */
    private function head_script($file,$ie=FALSE,$foreign=FALSE){
        $script = sprintf('<script src="%s.js"></script>',$foreign?$file:'/Min/'.$file);
        return empty($ie)?$script:'<!--[if '.$ie.']>'.$script.'<![endif]-->';
    }
    
    /**
     * 分解出含有外边引用的文件
     * @param string $files 文件
     * @param string $type 文件类型，css,或者js
     * @return string
     */
    private function resolve_http($files,$type='css'){
        $html = '';
        // 含http的外部链接，如http://cdn.fcmayi.com
        preg_match_all('@,?http[^,]+,?@',$files,$matchs);
        $method = 'head_'.($type == 'css'?'link':'script');
        foreach($matchs[0] as $match){
            $files = str_replace($match,',',$files);
            $foreign = str_replace(',', '', $match);
            $html .= $this->{$method}($foreign,FALSE,TRUE);
        }
    
        // 不含http但是不在public/css文件夹下
        preg_match_all('@,?(\/[^,]+)@',$files,$matchs);
        foreach($matchs[1] as $m){
            $files = str_replace($m,',',$files);
            $html .= $this->{$method}($m,FALSE,TRUE);
        }
    
        $files = trim(preg_replace('/,+/',',',$files),',');// 抽取掉外部引用后，可能造成多个，同时存在
        empty($files) || $html .= $this->{$method}($files);
        return $html;
    }
}

